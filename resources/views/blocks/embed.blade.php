<div class="modal fade" id="embedModal" tabindex="-1" role="dialog" aria-labelledby="embedModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="embedModalLabel">Embed {{ str_plural('Image', count($image)) }}</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <strong>URL</strong>
                        <textarea name="url_embed" rows="{{ (count($image) > 4 ? '5' : count($image)) }}" class="form-control">{{ image_embed_codes($image) }}</textarea>
                    </div>
                    <div class="form-group">
                        <strong>HTML</strong>
                        <textarea name="html_embed" rows="{{ (count($image) > 4 ? '5' : count($image)) }}" class="form-control">{{ image_embed_codes($image,'html') }}</textarea>
                    </div>
                    <div class="form-group">
                        <strong>BBCode</strong>
                        <textarea name="bbcode_embed" rows="{{ (count($image) > 4 ? '5' : count($image)) }}" class="form-control">{{ image_embed_codes($image,'bbcode') }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>