## Nombre del proyecto y problemática que resuelve:

Nombre: GymHub - Sistema de Gestión de Gimnasio, Inventario y Rutinas.
Problemática: Los gimnasios independientes requieren controlar tanto el acceso y las membresías de sus clientes como las ventas de mostrador. Actualmente esto se hace en libretas separadas o en Excel. GymHub unifica la administración de membresías, el control de inventario y punto de venta, y ofrece un valor agregado al proporcionar recomendaciones de rutinas consumiendo una API externa (Wger Workout Manager). Además, notificará a los usuarios sobre sus vencimientos vía correo, SMS y WhatsApp.

## Módulos principales (Entidades para BD MySQL):

Usuarios: Gestión de credenciales, roles, datos personales y fotografía.
Membresías: Catálogo de planes (mensual, semanal, visita).
Productos: Control de inventario de mostrador (aguas, dulces, suplementos).
Ventas: Registro de las transacciones (fecha, total, empleado que registró).
Venta_Detalle (N:M): Tabla pivote que relaciona una Venta con múltiples Productos o Membresías, permitiendo registrar exactamente qué artículos se compraron en un solo ticket.

## Roles de usuario:

Administrador: Acceso total. Gestión de empleados y usuarios (CRUD completo), administración del inventario, creación de membresías y revisión del corte del día.
Empleado: Acceso operativo. Puede registrar nuevos usuarios (no administradores), registrar ventas (membresías y productos) y generar recomendaciones de ejercicios usando la API externa.
Usuario: Acceso de cliente. Puede ver su perfil, subir su fotografía, consultar sus datos personales, ver los días restantes de su membresía en un formato de calendario y revisar las recomendaciones asignadas.

## Integrantes del equipo:

Uriel Espinoza de la Rosa

[Nombre de tu compañero/a]

## Link al repositorio de GitHub:

[Inserta aquí tu link de GitHub en Público]

## Link al tablero de GitHub Projects:

[Inserta aquí tu link de Projects en Público]
