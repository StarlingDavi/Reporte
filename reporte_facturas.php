<?php
include 'conexion.php';

$resultado = $conexion->query("SELECT * FROM factura");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Facturas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h2>Reporte de Facturas</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>DESCRIPCIÓN</th>
            <th>CATEGORÍA</th>
            <th>CANTIDAD</th>
            <th>PRECIO UNITARIO</th>
            <th>ITEBIS</th>
            <th>DESCUENTO</th>
            <th>TOTAL GENERAL</th>
            <th>ACCIONES</th>
        </tr>
        <?php while ($fila = mysqli_fetch_assoc($resultado)) : ?>
            <tr>
                <td><?= $fila['id'] ?></td>
                <td><?= $fila['descripcion'] ?></td>
                <td><?= $fila['categoria'] ?></td>
                <td><?= $fila['cantidad'] ?></td>   
                <td><?= number_format($fila['precio_unitario'], 2) ?></td>
                <td><?= number_format($fila['itebis'], 2) ?></td>
                <td><?= number_format($fila['descuento'], 2) ?></td>
                <td><?= number_format($fila['total_general'], 2) ?></td>
                <td>
                    <a href="imprimir_pdf.php?id=<?= $fila['id'] ?>" target="_blank" class="btn btn-sm btn-primary">Imprimir PDF</a>
                    <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta factura?')">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>