<div class="card">
    <div class="card-header card-header-white">
        <h6 class="mb-0 fw-semibold text-dark">
            <i class="bi bi-images"></i> Galería de Fotos
        </h6>
    </div>
    <div class="card-body">
        <?php if (empty($fotos)): ?>
            <div class="empty-state">
                <div class="icon-large"><i class="bi bi-image"></i></div>
                <div class="fw-semibold">Sin fotos disponibles</div>
                <div class="muted-small">Aún no se han cargado fotos</div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($fotos as $foto): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="gallery-item">
                            <a href="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($foto['archivo']); ?>" data-lightbox="galeria" data-title="<?php echo htmlspecialchars($foto['descripcion'] ?? 'Foto'); ?>">
                                <img src="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($foto['archivo']); ?>" alt="Foto">
                            </a>
                            <div class="meta">
                                <?php if (!empty($foto['descripcion'])): ?>
                                    <p class="mb-2 text-dark" style="font-size:0.9rem;">
                                        <?php echo htmlspecialchars($foto['descripcion']); ?>
                                    </p>
                                <?php endif; ?>
                                <small class="muted-small">
                                    <i class="bi bi-calendar"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($foto['fecha_carga'])); ?>
                                </small>
                                <?php if (isset($mostrar_eliminar) && $mostrar_eliminar): ?>
                                    <button type="button" class="btn btn-sm btn-danger float-end" onclick="eliminarFoto(<?php echo $foto['id']; ?>, '<?php echo $tabla ?? 'fotos_compra'; ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($mostrar_eliminar) && $mostrar_eliminar): ?>
<script>
function eliminarFoto(id, tabla) {
    if (confirm('¿Estás seguro de que deseas eliminar esta foto?')) {
        // Crear formulario para enviar
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="eliminar_foto" value="1">
            <input type="hidden" name="foto_id" value="${id}">
            <input type="hidden" name="foto_tabla" value="${tabla}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?php endif; ?>
