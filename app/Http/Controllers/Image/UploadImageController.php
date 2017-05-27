<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteImage;
use App\Models\Albums;
use App\Models\Images;
use App\Services\Filer;
use App\Services\Imager;
use Auth;
use File;
use Illuminate\Support\Str;

class UploadImageController extends Controller
{

    /**
     * @var Filer
     */
    private $filer;

    /**
     * @var Imager
     */
    private $imager;

    public function __construct()
    {
        parent::__construct();
        $this->filer = app(Filer::class);
        $this->imager = app(Imager::class);
    }

    public function create()
    {
        if ($images = $this->request->get('images')) {
            $expire = ($this->request->get('expire') >= 43800) ? 43800 : $this->request->get('expire');
            if (count($images) > 1) {
                $hash = $this->generateHash();
                while (Albums::where('hash', $hash)->first()) {
                    $hash = $this->generateHash();
                }

                $album = Albums::create([
                    'hash'              => $hash,
                    'album_title'       => !empty($this->request->get('title')) ? $this->request->get('title') : null,
                    'album_description' => !empty($this->request->get('description')) ? $this->request->get('description') : null,
                    'adult'             => !empty($this->request->get('adult')) ? 1 : 0,
                    'private'           => !empty($this->request->get('private')) ? 1 : 0,
                    'expire'            => !empty($expire) ? carbon()->addMinutes($expire) : null,
                    'created_by'        => (Auth::check()) ? Auth::id() : 1,
                ]);

                Images::whereIn('id', $images)->update([
                    'album_id' => $album->id,
                    'adult'    => !empty($this->request->get('adult')) ? 1 : 0,
                    'private'  => !empty($this->request->get('private')) ? 1 : 0,
                ]);

                return redirect('a/' . $album->hash);
            }

            $image = Images::find($images['0']);
            $image->image_title = !empty($this->request->get('title')) ? $this->request->get('title') : null;
            $image->image_description = !empty($this->request->get('description')) ? $this->request->get('description') : null;
            $image->adult = !empty($this->request->get('adult')) ? 1 : 0;
            $image->private = !empty($this->request->get('private')) ? 1 : 0;
            $image->expire = !empty($expire) ? carbon()->addMinutes($expire) : null;

            $image->save();

            return redirect('i/' . $image->hash);
        }

        return redirect()->back();
    }

    public function ajaxUpload()
    {
        $output = [];

        $image_file = $this->request->file('qqfile');

        if ($image_file->getSize() > computer_size(64, 'mb')) {
            $output['preventRetry'] = true;
            $output['error'] = 'File size exceeds 64mb';

            return response()->json($output);
        }

        $file_hash = sha1_file($image_file->getRealPath());

// TODO: Find a better way to handle duplicate uploads
//        if (!Auth::check()) {
//            $dupe_image = Images::where('file_hash', $file_hash)->where('created_by', 1)->first();
//            if ($dupe_image) {
//                $output['success'] = true;
//                $output['imageId'] = $dupe_image->id;
//
//                return response()->json($output, 200);
//            }
//        }

        $extension = mime_to_extension($image_file->getMimeType());
        if (!in_array($extension, ['jpg', 'gif', 'png'])) {
            $output['preventRetry'] = true;
            $output['error'] = 'invalid extension. Allowed: jpg, gif, png.';

            return response()->json($output);
        }

        try {
            $image = $this->imager->setImage($image_file);
        } catch (\Exception $e) {
            $output['preventRetry'] = true;
            $output['error'] = 'Failed to process image';

            return response()->json($output);
        }

        $hash = $this->generateHash(8);
        while (Images::where('hash', $hash)->first()) {
            $hash = $this->generateHash(8);
        }

        $this->filer->type('images')->put($hash . '.' . $extension, File::get($image_file->getRealPath()));

        $imagedb = Images::create([
            'hash'            => $hash,
            'file_hash'       => $file_hash,
            'image_title'     => $this->guessImageTitle($image_file->getClientOriginalName()),
            'image_extension' => $extension,
            'image_width'     => $image->getInfo()['width'],
            'image_height'    => $image->getInfo()['height'],
            'created_by'      => (Auth::check()) ? Auth::id() : 1,
        ]);

        $output['success'] = true;
        $output['imageId'] = $imagedb->id;

        return response()->json($output, 200);
    }

    public function ajaxDelete()
    {
        if (!Auth::check() || Auth::id() == 1) {
            return response()->json('Access Denied!', 403);
        }

        $id = (int)$this->request->get('id');
        if (empty($id)) {
            return response()->json('Invalid ID!', 422);
        }

        if ($this->request->get('action') == 'Album') {
            $album = Albums::with('images')->findOrFail($id);
            if ($album->created_by == Auth::id() || Auth::id() == 2) {
                if (!empty($album->images)) {
                    foreach ($album->images as $image) {
                        $this->dispatch(new DeleteImage($image->id));
                    }
                    $album->delete();

                    return response()->json('Album Deleted Successfully!', 200);
                }
            }
        }

        if ($this->request->get('action') == 'Image') {
            $image = Images::findOrFail($id);
            if ($image->created_by == Auth::id() || Auth::id() == 2) {
                $this->dispatch(new DeleteImage($image->id));
                $image->delete();

                return response()->json('Image Deleted Successfully!', 200);
            }
        }

        return response()->json('System Error!', 422);
    }

    private function generateHash($length = 6)
    {
        $hash = Str::random($length);
        while (in_array(strtolower($hash), $this->excluded_words())) {
            $hash = Str::random($length);
        }

        return $hash;
    }

    /**
     * @return array
     */
    private function excluded_words()
    {
        return [
            'abroad',
            'accept',
            'access',
            'across',
            'acting',
            'action',
            'active',
            'actual',
            'advice',
            'advise',
            'affect',
            'afford',
            'afraid',
            'agency',
            'agenda',
            'almost',
            'always',
            'amount',
            'animal',
            'annual',
            'answer',
            'anyone',
            'anyway',
            'appeal',
            'appear',
            'around',
            'arrive',
            'artist',
            'aspect',
            'assess',
            'assist',
            'assume',
            'attack',
            'attend',
            'august',
            'author',
            'avenue',
            'backed',
            'barely',
            'battle',
            'beauty',
            'became',
            'become',
            'before',
            'behalf',
            'behind',
            'belief',
            'belong',
            'berlin',
            'better',
            'beyond',
            'bishop',
            'border',
            'bottle',
            'bottom',
            'bought',
            'branch',
            'breath',
            'bridge',
            'bright',
            'broken',
            'budget',
            'burden',
            'bureau',
            'button',
            'camera',
            'cancer',
            'cannot',
            'carbon',
            'career',
            'castle',
            'casual',
            'caught',
            'center',
            'centre',
            'chance',
            'change',
            'charge',
            'choice',
            'choose',
            'chosen',
            'church',
            'circle',
            'client',
            'closed',
            'closer',
            'coffee',
            'column',
            'combat',
            'coming',
            'common',
            'comply',
            'copper',
            'corner',
            'costly',
            'county',
            'couple',
            'course',
            'covers',
            'create',
            'credit',
            'crisis',
            'custom',
            'damage',
            'danger',
            'dealer',
            'debate',
            'decade',
            'decide',
            'defeat',
            'defend',
            'define',
            'degree',
            'demand',
            'depend',
            'deputy',
            'desert',
            'design',
            'desire',
            'detail',
            'detect',
            'device',
            'differ',
            'dinner',
            'direct',
            'doctor',
            'dollar',
            'domain',
            'double',
            'driven',
            'driver',
            'during',
            'easily',
            'eating',
            'editor',
            'effect',
            'effort',
            'eighth',
            'either',
            'eleven',
            'emerge',
            'empire',
            'employ',
            'enable',
            'ending',
            'energy',
            'engage',
            'engine',
            'enough',
            'ensure',
            'entire',
            'entity',
            'equity',
            'escape',
            'estate',
            'ethnic',
            'exceed',
            'except',
            'excess',
            'expand',
            'expect',
            'expert',
            'export',
            'extend',
            'extent',
            'fabric',
            'facing',
            'factor',
            'failed',
            'fairly',
            'fallen',
            'family',
            'famous',
            'father',
            'fellow',
            'female',
            'figure',
            'filing',
            'finger',
            'finish',
            'fiscal',
            'flight',
            'flying',
            'follow',
            'forced',
            'forest',
            'forget',
            'formal',
            'format',
            'former',
            'foster',
            'fought',
            'fourth',
            'French',
            'friend',
            'future',
            'garden',
            'gather',
            'gender',
            'german',
            'global',
            'golden',
            'ground',
            'growth',
            'guilty',
            'handed',
            'handle',
            'happen',
            'hardly',
            'headed',
            'health',
            'height',
            'hidden',
            'holder',
            'honest',
            'impact',
            'import',
            'income',
            'indeed',
            'injury',
            'inside',
            'intend',
            'intent',
            'invest',
            'island',
            'itself',
            'jersey',
            'joseph',
            'junior',
            'killed',
            'labour',
            'latest',
            'latter',
            'launch',
            'lawyer',
            'leader',
            'league',
            'leaves',
            'legacy',
            'length',
            'lesson',
            'letter',
            'lights',
            'likely',
            'linked',
            'liquid',
            'listen',
            'little',
            'living',
            'losing',
            'lucent',
            'luxury',
            'mainly',
            'making',
            'manage',
            'manner',
            'manual',
            'margin',
            'marine',
            'marked',
            'market',
            'martin',
            'master',
            'matter',
            'mature',
            'medium',
            'member',
            'memory',
            'mental',
            'merely',
            'merger',
            'method',
            'middle',
            'miller',
            'mining',
            'minute',
            'mirror',
            'mobile',
            'modern',
            'modest',
            'module',
            'moment',
            'morris',
            'mostly',
            'mother',
            'motion',
            'moving',
            'murder',
            'museum',
            'mutual',
            'myself',
            'narrow',
            'nation',
            'native',
            'nature',
            'nearby',
            'nearly',
            'nights',
            'nobody',
            'normal',
            'notice',
            'notion',
            'number',
            'object',
            'obtain',
            'office',
            'offset',
            'online',
            'option',
            'orange',
            'origin',
            'output',
            'oxford',
            'packed',
            'palace',
            'parent',
            'partly',
            'patent',
            'people',
            'period',
            'permit',
            'person',
            'phrase',
            'picked',
            'planet',
            'player',
            'please',
            'plenty',
            'pocket',
            'police',
            'policy',
            'prefer',
            'pretty',
            'prince',
            'prison',
            'profit',
            'proper',
            'proven',
            'public',
            'pursue',
            'raised',
            'random',
            'rarely',
            'rather',
            'rating',
            'reader',
            'really',
            'reason',
            'recall',
            'recent',
            'record',
            'reduce',
            'reform',
            'regard',
            'regime',
            'region',
            'relate',
            'relief',
            'remain',
            'remote',
            'remove',
            'repair',
            'repeat',
            'replay',
            'report',
            'rescue',
            'resort',
            'result',
            'retail',
            'retain',
            'return',
            'reveal',
            'review',
            'reward',
            'riding',
            'rising',
            'robust',
            'ruling',
            'safety',
            'salary',
            'sample',
            'saving',
            'saying',
            'scheme',
            'school',
            'screen',
            'search',
            'season',
            'second',
            'secret',
            'sector',
            'secure',
            'seeing',
            'select',
            'seller',
            'senior',
            'series',
            'server',
            'settle',
            'severe',
            'sexual',
            'should',
            'signal',
            'signed',
            'silent',
            'silver',
            'simple',
            'simply',
            'single',
            'sister',
            'slight',
            'smooth',
            'social',
            'solely',
            'sought',
            'source',
            'soviet',
            'speech',
            'spirit',
            'spoken',
            'spread',
            'spring',
            'square',
            'stable',
            'status',
            'steady',
            'stolen',
            'strain',
            'stream',
            'street',
            'stress',
            'strict',
            'strike',
            'string',
            'strong',
            'struck',
            'studio',
            'submit',
            'sudden',
            'suffer',
            'summer',
            'summit',
            'supply',
            'surely',
            'survey',
            'switch',
            'symbol',
            'system',
            'taking',
            'talent',
            'target',
            'taught',
            'tenant',
            'tender',
            'tennis',
            'thanks',
            'theory',
            'thirty',
            'though',
            'threat',
            'thrown',
            'ticket',
            'timely',
            'timing',
            'tissue',
            'toward',
            'travel',
            'treaty',
            'trying',
            'twelve',
            'twenty',
            'unable',
            'unique',
            'united',
            'unless',
            'unlike',
            'update',
            'useful',
            'valley',
            'varied',
            'vendor',
            'versus',
            'victim',
            'vision',
            'visual',
            'volume',
            'walker',
            'wealth',
            'weekly',
            'weight',
            'wholly',
            'window',
            'winner',
            'winter',
            'within',
            'wonder',
            'worker',
            'wright',
            'writer',
            'yellow',
        ];
    }

    private function guessImageTitle($name)
    {
        $filename = pathinfo($name, PATHINFO_FILENAME);
        $filename = str_replace('_', '-', $filename);

        return title_case(str_slug($filename, ' '));
    }
}