# 🏋️‍♂️ GymHub - Sistema de Gestión de Gimnasio, Inventario y Rutinas

## Problemática que resuelve
Los gimnasios independientes requieren controlar tanto el acceso y las membresías de sus clientes como las ventas de mostrador. Actualmente, esto se hace de manera rudimentaria en libretas separadas o en hojas de cálculo. 

**GymHub** unifica la administración de membresías, el control de inventario y el punto de venta en una sola plataforma. Además, ofrece un valor agregado al proporcionar recomendaciones de rutinas consumiendo una API externa (*Wger Workout Manager*) y notifica automáticamente a los usuarios sobre los vencimientos de sus pagos vía **correo electrónico, SMS y WhatsApp**.

---

## Módulos Principales (Base de Datos MySQL)
* **Usuarios:** Gestión de credenciales, roles, datos personales y fotografía de perfil.
* **Membresías:** Catálogo de planes disponibles (mensual, semanal, visita).
* **Productos:** Control de inventario físico de mostrador (aguas, dulces, suplementos).
* **Ventas:** Registro del historial de transacciones (fecha, total, empleado responsable).
* **Venta_Detalle (N:M):** Tabla pivote que relaciona una *Venta* con múltiples *Productos* o *Membresías*, permitiendo registrar exactamente qué artículos se incluyeron en un mismo ticket.

---

## Roles de Usuario
**Administrador:** Acceso total al sistema. Gestión de empleados y usuarios (CRUD completo), administración general del inventario, creación de membresías y revisión financiera del corte del día.
**Empleado:** Acceso operativo. Puede registrar nuevos usuarios (rol cliente), registrar ventas (membresías y productos) y generar recomendaciones de ejercicios usando la API externa.
**Usuario:** Acceso de cliente. Puede visualizar su perfil, actualizar su fotografía, consultar sus datos personales, revisar los días restantes de su membresía mediante un calendario interactivo y consultar las rutinas recomendadas.

---

## Integrantes del Equipo
* Uriel Espinoza de la Rosa
* [Nombre de tu compañero/a]

---

## 🔗 Enlaces Importantes
* **Repositorio Código Fuente:** [Inserta aquí tu link de GitHub en Público]
* **Tablero de Tareas (Backlog):** [https://github.com/users/Uriel-tech302/projects/2/views/1]
