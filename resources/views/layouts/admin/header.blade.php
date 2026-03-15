<!-- Header Section starts -->
<header class="header-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-8 col-sm-6 d-flex align-items-center header-left p-0">
                <span class="header-toggle">
                    <i class="ph ph-squares-four"></i>
                </span>

            </div>

            <div class="col-4 col-sm-6 d-flex align-items-center justify-content-end header-right p-0">

                {{-- Botón pantalla completa --}}
                <button id="btn-fullscreen" title="Pantalla completa"
                        onclick="toggleFullscreen()"
                        style="background:none;border:none;cursor:pointer;padding:6px 8px;color:#4a5568;border-radius:7px;transition:all .15s;"
                        onmouseover="this.style.background='#edf2f7'" onmouseout="this.style.background='none'">
                    <i id="fs-icon" class="ti ti-maximize" style="font-size:1.25rem;"></i>
                </button>

            </div>
        </div>
    </div>
</header>
<!-- Header Section ends -->
