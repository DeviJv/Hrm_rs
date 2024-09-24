<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="{{ asset('errors/style.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('errors/oxygen.fonts.css') }}">
<div class="container">
    <div class="error">
        <h1>404</h1>
        <h2>error</h2>
        <p>Ops halaman yang anda cari tidak bisa kita temukan</p>
        <p class="subtitle">ERR_NOT_FOUND</p>
        {{-- <a href="{{ route('/') }}">Kembali</a> --}}
    </div>
    <div class="stack-container">
        <div class="card-container">
            <div class="perspec" style="--spreaddist: 125px; --scaledist: .75; --vertdist: -25px;">
                <div class="card">
                    <div class="writing">
                        <div class="topbar">
                            <div class="red"></div>
                            <div class="yellow"></div>
                            <div class="green"></div>
                        </div>
                        <div class="code">
                            <div class="centered">
                                <img src="{{ asset('errors/sign_question.png') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-container">
            <div class="perspec" style="--spreaddist: 100px; --scaledist: .8; --vertdist: -20px;">
                <div class="card">
                    <div class="writing">
                        <div class="topbar">
                            <div class="red"></div>
                            <div class="yellow"></div>
                            <div class="green"></div>
                        </div>
                        <div class="code">
                            <div class="centered">
                                <img src="{{ asset('errors/sign_question.png') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-container">
            <div class="perspec" style="--spreaddist:75px; --scaledist: .85; --vertdist: -15px;">
                <div class="card">
                    <div class="writing">
                        <div class="topbar">
                            <div class="red"></div>
                            <div class="yellow"></div>
                            <div class="green"></div>
                        </div>
                        <div class="code">
                            <div class="centered">
                                <img src="{{ asset('errors/sign_question.png') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-container">
            <div class="perspec" style="--spreaddist: 50px; --scaledist: .9; --vertdist: -10px;">
                <div class="card">
                    <div class="writing">
                        <div class="topbar">
                            <div class="red"></div>
                            <div class="yellow"></div>
                            <div class="green"></div>
                        </div>
                        <div class="code">
                            <div class="centered">
                                <img src="{{ asset('errors/sign_question.png') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-container">
            <div class="perspec" style="--spreaddist: 25px; --scaledist: .95; --vertdist: -5px;">
                <div class="card">
                    <div class="writing">
                        <div class="topbar">
                            <div class="red"></div>
                            <div class="yellow"></div>
                            <div class="green"></div>
                        </div>
                        <div class="code">
                            <div class="centered">
                                <img src="{{ asset('errors/sign_question.png') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-container">
            <div class="perspec" style="--spreaddist: 0px; --scaledist: 1; --vertdist: 0px;">
                <div class="card">
                    <div class="writing">
                        <div class="topbar">
                            <div class="red"></div>
                            <div class="yellow"></div>
                            <div class="green"></div>
                        </div>
                        <div class="code">
                            <div class="centered">
                                <img src="{{ asset('errors/sign_question.png') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p class="copyright">©2024 Quantum Tech Solutions</p>
    </footer>
</div>
<style>
    footer {
        position: fixed;
        bottom: 0;
        height: 40px;
        border-top-color: #33333342;
        border-top-style: solid;
        border-top-width: 1px;
        width: 100%;
    }

    p.copyright {
        position: absolute;
        margin: 0;
        top: 9px;
        width: 100%;
        color: #000000a3;
        font-size: 1em;
        text-align: center;
        bottom: 0;
    }
</style>
<script type="text/javascript" src="{{ asset('errors/script.js') }}"></script>
