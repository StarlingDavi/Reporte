
Este documento explica detalladamente cómo se crean y funcionan los principales archivos del proyecto de facturación en C#. A continuación se presenta cada archivo en orden y con su respectiva explicación.

---

## 1. `Form1.cs`

Este es el formulario principal de la aplicación. Su propósito es mostrar una tabla (`DataGridView`) con los datos de las facturas y permitir al usuario agregar, actualizar, eliminar o ver reportes de las mismas.

### Principales métodos:

- `RefreshData()`: Recarga los datos desde la base de datos usando `Conexion.LeerFacturas()`.
- `Form1_Load`: Carga los datos al iniciar el formulario.
- `agregarBtn_Click`: Abre el formulario `AgregarFactura` y actualiza la vista.
- `actualizarBtn_Click`: Toma los datos de la fila seleccionada, los pasa al formulario `ActualizarFactura` y actualiza la base de datos.
- `eliminarBtn_Click`: Elimina la factura seleccionada en la base de datos.
- `reporteBtn_Click`: Abre el formulario `ReporteForm` para mostrar reportes.

### 🔽 Inserta aquí el código completo de `Form1.cs`:
```csharp
using ReportesProyecto.Reportes;
using System;
using System.Windows.Forms;

namespace ReportesProyecto
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
        }

        private void RefreshData()
        {
            dataGridView1.DataSource = Conexion.LeerFacturas();
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            RefreshData();
        }

        private void agregarBtn_Click(object sender, EventArgs e)
        {
            AgregarFactura form = new AgregarFactura();
            form.ShowDialog();
            RefreshData();
        }

        private void actualizarBtn_Click(object sender, EventArgs e)
        {
            if (dataGridView1.SelectedRows.Count == 0)
            {
                MessageBox.Show("Por favor seleccione una fila.", "Advertencia", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var selectedRow = dataGridView1.SelectedRows[0];

            try
            {

                int id = Convert.ToInt32(selectedRow.Cells["ID"].Value?.ToString().Trim());
                string descripcion = selectedRow.Cells["Descripcion"].Value?.ToString().Trim();
                string categoria = selectedRow.Cells["Categoria"].Value?.ToString().Trim();
                int cantidad = Convert.ToInt32(selectedRow.Cells["Cantidad"].Value);
                decimal precioUnitario = Convert.ToDecimal(selectedRow.Cells["Precio_Unitario"].Value);
                decimal itbis = Convert.ToDecimal(selectedRow.Cells["Itebis"].Value);
                decimal descuento = Convert.ToDecimal(selectedRow.Cells["Descuento"].Value);
                decimal totalGeneral = Convert.ToDecimal(selectedRow.Cells["Total_General"].Value);

                ActualizarFactura detalleForm = new ActualizarFactura(id, descripcion, categoria, cantidad, precioUnitario, itbis, descuento, totalGeneral);
                detalleForm.ShowDialog();
                RefreshData();
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Error al obtener los datos de la fila seleccionada: {ex.Message}", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void eliminarBtn_Click(object sender, EventArgs e)
        {
            if (dataGridView1.SelectedRows.Count == 0)
            {
                MessageBox.Show("Por favor seleccione una fila.", "Advertencia", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var selectedRow = dataGridView1.SelectedRows[0];

            try
            {
                int id = Convert.ToInt32(selectedRow.Cells["ID"].Value?.ToString());
                Conexion.Eliminar(id);
                RefreshData();
            }catch(Exception ex)
            {
                MessageBox.Show($"Error al obtener el identificador de la fila seleccionada: {ex.Message}", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void reporteBtn_Click(object sender, EventArgs e)
        {
            ReporteForm form = new ReporteForm();
            form.ShowDialog();
        }
    }
}
```

---

## 2. `AgregarFactura.cs`

Este formulario permite al usuario ingresar los datos de una nueva factura.

### Lógica principal:

- Recoge los datos desde los campos del formulario.
- Valida que todos los datos estén completos y tengan el tipo correcto.
- Llama al método `Conexion.AgregarFactura()` para insertar la nueva factura en la base de datos.

### 🔽 Inserta aquí el código completo de `AgregarFactura.cs`:
```csharp
using System; 
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace ReportesProyecto
{
    public partial class AgregarFactura : Form
    {
        public AgregarFactura()
        {
            InitializeComponent();
        }

        private void agregarBtn_Click(object sender, EventArgs e)
        {
            string descripcion = descripcionBox.Text.Trim();
            string categoria = categoriaBox.Text.Trim();
            string cantidadText = cantidadBox.Text.Trim();
            string precioUnitarioText = precioUnitarioBox.Text.Trim();
            string itbisText = itbisBox.Text.Trim();
            string descuentoText = descuentoBox.Text.Trim();
            string totalGeneralText = totalGeneralBox.Text.Trim();

            if (string.IsNullOrEmpty(descripcion) || string.IsNullOrEmpty(categoria) ||
                string.IsNullOrEmpty(cantidadText) || string.IsNullOrEmpty(precioUnitarioText) ||
                string.IsNullOrEmpty(itbisText) || string.IsNullOrEmpty(descuentoText) ||
                string.IsNullOrEmpty(totalGeneralText))
            {
                MessageBox.Show("Todos los campos son obligatorios.", "Advertencia", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (!int.TryParse(cantidadText, out int cantidad))
            {
                MessageBox.Show("La cantidad debe ser un número entero válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!decimal.TryParse(precioUnitarioText, out decimal precioUnitario))
            {
                MessageBox.Show("El precio unitario debe ser un número decimal válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!decimal.TryParse(itbisText, out decimal itbis))
            {
                MessageBox.Show("El ITBIS debe ser un número decimal válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!decimal.TryParse(descuentoText, out decimal descuento))
            {
                MessageBox.Show("El descuento debe ser un número decimal válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!decimal.TryParse(totalGeneralText, out decimal totalGeneral))
            {
                MessageBox.Show("El total general debe ser un número decimal válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            try
            {
                Conexion.AgregarFactura(descripcion, categoria, cantidad, precioUnitario, itbis, descuento, totalGeneral);
                MessageBox.Show("Factura agregada correctamente.", "Éxito", MessageBoxButtons.OK, MessageBoxIcon.Information);
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Ocurrió un error al agregar la factura: {ex.Message}", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }
    }
} 
```

---

## 3. `ActualizarFactura.cs`

Este formulario permite al usuario actualizar los datos de una factura existente.

### Características:

- Recibe los datos de la factura como parámetros al constructor.
- Carga los datos en los campos correspondientes.
- Valida los datos ingresados por el usuario.
- Llama a `Conexion.ActualizarFactura()` con los nuevos valores para actualizar la base de datos.

### 🔽 Inserta aquí el código completo de `ActualizarFactura.cs`:
```csharp
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace ReportesProyecto
{
    public partial class ActualizarFactura : Form
    {
        public int Id;

        public ActualizarFactura(int id, string descripcion, string categoria, int cantidad, decimal precioUnitario, decimal itbis, decimal descuento, decimal totalGeneral)
        {
            InitializeComponent();
            Id = id;
            descripcionBox.Text = descripcion;
            categoriaBox.Text = categoria;
            cantidadBox.Text = cantidad.ToString();
            precioUnitarioBox.Text = precioUnitario.ToString("0.00");
            itbisBox.Text = itbis.ToString("0.00");
            descuentoBox.Text = descuento.ToString("0.00");
            totalGeneralBox.Text = totalGeneral.ToString("0.00");
        }

        private void actualizarBtn_Click(object sender, EventArgs e)
        {
            string descripcion = descripcionBox.Text.Trim();
            string categoria = categoriaBox.Text.Trim();
            string cantidadText = cantidadBox.Text.Trim();
            string precioUnitarioText = precioUnitarioBox.Text.Trim();
            string itbisText = itbisBox.Text.Trim();
            string descuentoText = descuentoBox.Text.Trim();
            string totalGeneralText = totalGeneralBox.Text.Trim();

            if (string.IsNullOrEmpty(descripcion) || string.IsNullOrEmpty(categoria) ||
                string.IsNullOrEmpty(cantidadText) || string.IsNullOrEmpty(precioUnitarioText) ||
                string.IsNullOrEmpty(itbisText) || string.IsNullOrEmpty(descuentoText) ||
                string.IsNullOrEmpty(totalGeneralText))
            {
                MessageBox.Show("Todos los campos son obligatorios.", "Advertencia", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (!int.TryParse(cantidadText, out int cantidad))
            {
                MessageBox.Show("La cantidad debe ser un número entero válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!decimal.TryParse(precioUnitarioText, out decimal precioUnitario))
            {
                MessageBox.Show("El precio unitario debe ser un número decimal válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!decimal.TryParse(itbisText, out decimal itbis))
            {
                MessageBox.Show("El ITBIS debe ser un número decimal válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!decimal.TryParse(descuentoText, out decimal descuento))
            {
                MessageBox.Show("El descuento debe ser un número decimal válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            if (!decimal.TryParse(totalGeneralText, out decimal totalGeneral))
            {
                MessageBox.Show("El total general debe ser un número decimal válido.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            try
            {
                Conexion.ActualizarFactura(Id, descripcion, categoria, cantidad, precioUnitario, itbis, descuento, totalGeneral);
                MessageBox.Show("Factura agregada correctamente.", "Éxito", MessageBoxButtons.OK, MessageBoxIcon.Information);
                Close();
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Ocurrió un error al actualizar la factura: {ex.Message}", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }
    }
} 
```

---

## 4. `Conexion.cs`

Clase estática que maneja la conexión con la base de datos y contiene métodos CRUD.

### Métodos disponibles:

- `AgregarFactura(...)`: Inserta una nueva factura en la tabla `Facturas`.
- `Eliminar(int id)`: Elimina la factura con el ID dado.
- `ActualizarFactura(...)`: Actualiza una factura existente en la base de datos.
- `LeerFacturas()`: **Nota**: Hay un error aquí, ya que este método tiene un `INSERT` en lugar de un `SELECT`. Debería realizar una consulta `SELECT * FROM Facturas` para retornar los datos al `DataGridView`.

> ⚠️ **Importante**: Corregir el método `LeerFacturas()` para que devuelva los datos correctamente.

### 🔽 Inserta aquí el código completo de `Conexion.cs`:
```csharp
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SqlClient;
using System.Security.Policy;
using System.Data;

namespace ReportesProyecto
{
    public class Conexion
    {
        // Ingresen la conexion a la bd aqui para la tabla 'Facturas', Cuando les funcione eliminen este comentario
        private static string stringConnection = "Data Source=NombreDelServer;Initial Catalog=NombreBD;Integrated Security=True;";

        public static void AgregarFactura(string Descripcion, string Categoria, int Cantidad, decimal Precio_Unitario, decimal Itebis, decimal Descuento, decimal Total_General)
        {
            using (SqlConnection conn = new SqlConnection(stringConnection))
            {
                conn.Open();
                SqlCommand cmd = new SqlCommand("INSERT INTO Facturas(Descripcion, Categoria, Cantidad, Precio_Unitario, Itebis, Descuento, Total_General) " +
                    "VALUES (@Descripcion, @Categoria, @Cantidad, @Precio_Unitario, @Itebis, @Descuento, @Total_General)");
                
                cmd.Parameters.AddWithValue("@Descripcion", Descripcion);
                cmd.Parameters.AddWithValue("@Categoria", Categoria);
                cmd.Parameters.AddWithValue("@Cantidad", Cantidad);
                cmd.Parameters.AddWithValue("@Precio_Unitario", Precio_Unitario);
                cmd.Parameters.AddWithValue("@Itebis", Itebis);
                cmd.Parameters.AddWithValue("@Descuento", Descuento);
                cmd.Parameters.AddWithValue("@Total_General", Total_General);
                
                cmd.ExecuteNonQuery();
            }
        }
        public static void Eliminar(int id)
        {
            using (SqlConnection conn = new SqlConnection(stringConnection))
            {
                conn.Open();
                SqlCommand cmd = new SqlCommand("DELETE FROM Facturas WHERE ID=@id");

                cmd.Parameters.AddWithValue("@id", id);
                cmd.ExecuteNonQuery();
            }
        }
        public static void ActualizarFactura(int id, string Descripcion, string Categoria, int Cantidad, decimal Precio_Unitario, decimal Itebis, decimal Descuento, decimal Total_General)
        {
            using (SqlConnection conn = new SqlConnection(stringConnection))
            {
                conn.Open();
                SqlCommand cmd = new SqlCommand("UPDATE Facturas SET Descripcion=@Descripcion, Categoria=@Categoria, Cantidad=@Cantidad, Precio_Unitario=@Precio_Unitario, Itebis=@Itebis, Descuento=@Descuento, Total_General=@Total_General WHERE ID=@id");

                cmd.Parameters.AddWithValue("@id", id);
                cmd.Parameters.AddWithValue("@Descripcion", Descripcion);
                cmd.Parameters.AddWithValue("@Categoria", Categoria);
                cmd.Parameters.AddWithValue("@Cantidad", Cantidad);
                cmd.Parameters.AddWithValue("@Precio_Unitario", Precio_Unitario);
                cmd.Parameters.AddWithValue("@Itebis", Itebis);
                cmd.Parameters.AddWithValue("@Descuento", Descuento);
                cmd.Parameters.AddWithValue("@Total_General", Total_General);

                cmd.ExecuteNonQuery();
            }
        }
        public static DataSet LeerFacturas()
        {
            using (SqlConnection conn = new SqlConnection(stringConnection))
            {
                conn.Open();
                SqlCommand cmd = new SqlCommand("INSERT INTO Facturas(Descripcion, Categoria, Cantidad, Precio_Unitario, Itebis, Descuento, Total_General) " +
                    "VALUES (@Descripcion, @Categoria, @Cantidad, @Precio_Unitario, @Itebis, @Descuento, @Total_General)");

                SqlDataAdapter adapter = new SqlDataAdapter(cmd);
                DataSet dt = new DataSet();
                adapter.Fill(dt);

                return dt;
            }
        }
    }
}
```

---

## 5. `ReporteForm.cs`

Formulario que contiene el `ReportViewer` de Visual Studio para visualizar reportes.

### Detalles:

- `ReporteForm_Load`: Método que refresca el control de reportes para mostrar el contenido cargado.

### 🔽 Inserta código
```csharp
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace ReportesProyecto.Reportes
{
    public partial class ReporteForm : Form
    {
        public ReporteForm()
        {
            InitializeComponent();
        }

        private void ReporteForm_Load(object sender, EventArgs e)
        {

            this.reportViewer1.RefreshReport();
        }
    }
}
```




## `ReporteForm.cs`

Este archivo representa un formulario independiente utilizado para visualizar **reportes** utilizando el control `ReportViewer`, que forma parte de `Microsoft.Reporting.WinForms`.

---

### Importación de namespaces

```csharp
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
```

Estas bibliotecas permiten:

- Controlar elementos gráficos (como formularios, controles y eventos),
- Usar funcionalidades generales del sistema (como fechas, datos, listas, etc.),
- Implementar el formulario con Windows Forms.

---

### Declaración del namespace y clase

```csharp
namespace ReportesProyecto.Reportes
{
    public partial class ReporteForm : Form
    {
```

- El formulario está dentro del namespace `ReportesProyecto.Reportes`, lo que sugiere una estructura organizada del proyecto en carpetas o módulos.
- La clase `ReporteForm` hereda de `Form`, lo que significa que es un formulario gráfico de Windows.

---

### Constructor de la clase

```csharp
public ReporteForm()
{
    InitializeComponent();
}
```

- El constructor llama a `InitializeComponent()`, un método que crea y configura todos los controles visuales definidos en el diseñador del formulario (por ejemplo, el `ReportViewer`).

---

### Método `ReporteForm_Load`

```csharp
private void ReporteForm_Load(object sender, EventArgs e)
{
    this.reportViewer1.RefreshReport();
}
```

- Este evento se ejecuta automáticamente cuando el formulario se carga.
- El método `RefreshReport()` obliga al control `reportViewer1` a renderizar el informe configurado (generalmente asociado a un archivo `.rdlc`).
- Este método es útil si el informe necesita mostrarse con datos actualizados cada vez que se abre el formulario.

---


## 🔌 Instalar el Paquete NuGet `MySql.Data`

### Opción A: Usando el Administrador de Paquetes NuGet (Visual Studio)

1. Abre tu proyecto en Visual Studio.
2. Haz clic derecho en el proyecto → **"Administrar paquetes NuGet"**.

![alt text](image.png)

![alt text](image-1.png)

3. Ve a la pestaña **"Examinar"**.

![alt text](image-2.png)

4. Busca `MySql.Data`.

![alt text](image-3.png)

5. Selecciona el paquete publicado por **Oracle**.
6. Haz clic en **"Instalar"**.
7. Acepta los términos de licencia si es necesario.

### Opción B: Usando la Consola del Administrador de Paquetes (Visual Studio)

1. Abre el menú: **Herramientas → Administrador de paquetes NuGet → Consola del Administrador de paquetes**.
2. Escribe `Install-Package MySql.Data` y presiona **Enter**.

---


## 🧾 Verificación

- Ejecuta tu aplicación.
- Asegúrate de que no ocurran errores de instalación.
- Verifica que el paquete `MySql.Data` aparece en las dependencias del proyecto.

---

## 📝 Notas Finales

- No necesitas instalar MySQL Server en la misma máquina, pero asegúrate de que la base de datos esté disponible.
- El paquete `MySql.Data` incluye todo lo necesario para conectarte y trabajar con bases de datos MySQL desde C#.
---

## Conclusión

Este conjunto de formularios y clases permite gestionar de manera sencilla las facturas de una base de datos local mediante operaciones CRUD (Crear, Leer, Actualizar, Eliminar) y visualizar reportes.

Recuerda ajustar la cadena de conexión (`stringConnection`) en `Conexion.cs` con los datos reales del servidor y base de datos.
