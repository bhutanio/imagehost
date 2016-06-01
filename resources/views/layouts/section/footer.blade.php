<footer class="footer">
    <div class="container footer-top">
        <div class="row">
            <div class="col-md-6 footer-col text-center">
                <h3>GitHub <a href="https://github.com/bhutanio/imagehost" title="imagehost GitHub Repo" target="_blank">Repo</a></h3>
                <ul class="list-inline">
                    <li>
                        <a class="github-button" href="https://github.com/bhutanio/imagehost" data-icon="octicon-star" data-style="mega" data-count-href="/bhutanio/imagehost/stargazers" data-count-api="/repos/bhutanio/imagehost#stargazers_count" data-count-aria-label="# stargazers on GitHub" aria-label="Star bhutanio/imagehost on GitHub">Star</a>
                    </li>
                    <li>
                        <a class="github-button" href="https://github.com/bhutanio/imagehost/archive/master.zip" data-icon="octicon-cloud-download" data-style="mega" aria-label="Download bhutanio/imagehost on GitHub">Download</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-6 footer-col text-center">
                <h3>About {{ env('SITE_NAME') }}</h3>
                <p>{{ env('SITE_NAME') }} is a free to use, open source Image Hosting service, <br>created by
                    <a href="http://bhutan.io" title="Bhutan.io">bhutan.io</a></p>
            </div>
        </div>
    </div>
</footer>
<div class="footer-bottom">
    <div class="col-lg-12 text-center">
        <p>Copyright &copy; {{ env('SITE_NAME') }}</p>
        <ul class="list-inline">
            <li class=""><a href="{{ url('report') }}">Report</a></li>
            <li class=""><a href="{{ url('about') }}">About</a></li>
        </ul>
    </div>
</div>
