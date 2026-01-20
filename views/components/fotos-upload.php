<div class="card">
    <div class="card-header card-header-white">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="bi bi-images"></i> Fotos <?php echo $tipo_fotos ?? 'del Producto'; ?>
        </h6>
    </div>
    <div class="card-body">
        <!-- Area de carga de fotos -->
        <div class="mb-3">
            <label class="form-label">Subir Fotos (Opcional)</label>
            <div class="upload-area" id="uploadArea<?php echo $id_zona ?? ''; ?>">
                <input type="file" id="inputFotos<?php echo $id_zona ?? ''; ?>" class="d-none" multiple accept="image/*">
                <div class="icon-large"><i class="bi bi-cloud-upload"></i></div>
                <div class="title">Arrastra fotos aquí o haz clic para seleccionar</div>
                <div class="desc">PNG, JPG, GIF o WebP (máximo 5MB por foto)</div>
            </div>
        </div>

        <!-- Vista previa de fotos -->
        <div id="fotosPreview<?php echo $id_zona ?? ''; ?>" class="row g-3">
            <!-- Las fotos se mostrarán aquí -->
        </div>

        <!-- Input oculto para formulario -->
        <input type="hidden" name="fotos_cargadas<?php echo $id_zona ?? ''; ?>" id="fotosCargadas<?php echo $id_zona ?? ''; ?>" value="[]">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const idZona = '<?php echo $id_zona ?? ''; ?>';
    const uploadArea = document.getElementById('uploadArea' + idZona);
    const inputFotos = document.getElementById('inputFotos' + idZona);
    const fotosPreview = document.getElementById('fotosPreview' + idZona);
    const fotosCargadasInput = document.getElementById('fotosCargadas' + idZona);
    
    let fotosCargadas = [];

    // Click en área de carga
    uploadArea.addEventListener('click', () => inputFotos.click());

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#34c759';
        uploadArea.style.background = '#f0f9ff';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#0071e3';
        uploadArea.style.background = 'transparent';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#0071e3';
        uploadArea.style.background = 'transparent';
        procesarArchivos(e.dataTransfer.files);
    });

    // Selección de archivos
    inputFotos.addEventListener('change', (e) => {
        procesarArchivos(e.target.files);
    });

    function procesarArchivos(archivos) {
        Array.from(archivos).forEach((archivo) => {
            if (archivo.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = (e) => {
                    const id = 'foto_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    
                    const fotoObj = {
                        id: id,
                        archivo: archivo,
                        preview: e.target.result,
                        descripcion: ''
                    };
                    
                    fotosCargadas.push(fotoObj);
                    actualizarPreview();
                };
                
                reader.readAsDataURL(archivo);
            }
        });
        
        // Limpiar input
        inputFotos.value = '';
    }

    function actualizarPreview() {
        fotosPreview.innerHTML = '';
        
        if (fotosCargadas.length === 0) {
            fotosPreview.innerHTML = '<div class="col-12 preview-empty">Sin fotos cargadas</div>';
            fotosCargadasInput.value = '[]';
            return;
        }

        fotosCargadas.forEach((foto, index) => {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4';
            col.innerHTML = `
                <div class="preview-item position-relative">
                    <img src="${foto.preview}" alt="Foto ${index + 1}" class="img-cover">
                    <div style="position: absolute; top: 0; right: 0; padding: 0.5rem;">
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFoto('${foto.id}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="footer">
                        <textarea class="form-control form-control-sm" placeholder="Descripción (opcional)" rows="2" onchange="actualizarDescripcion('${foto.id}', this.value)"></textarea>
                    </div>
                </div>
            `;
            fotosPreview.appendChild(col);
        });

        // Actualizar input oculto
        const datosEnvio = fotosCargadas.map((f, i) => ({
            index: i,
            descripcion: f.descripcion,
            nombre_original: f.archivo.name
        }));
        fotosCargadasInput.value = JSON.stringify(datosEnvio);
    }

    window.eliminarFoto = function(fotoId) {
        fotosCargadas = fotosCargadas.filter(f => f.id !== fotoId);
        actualizarPreview();
    };

    window.actualizarDescripcion = function(fotoId, descripcion) {
        const foto = fotosCargadas.find(f => f.id === fotoId);
        if (foto) {
            foto.descripcion = descripcion;
            fotosCargadasInput.value = JSON.stringify(fotosCargadas.map((f, i) => ({
                index: i,
                descripcion: f.descripcion,
                nombre_original: f.archivo.name
            })));
        }
    };

    // Exponer la función para obtener fotos
    window.obtenerFotosCargadas_<?php echo $id_zona ?? ''; ?> = function() {
        return fotosCargadas;
    };
});
</script>
