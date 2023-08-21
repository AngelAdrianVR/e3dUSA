<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'Ver catalogo de productos', 'category' => 'Catalogo_de_productos']);
        Permission::create(['name' => 'Crear catalogo de productos', 'category' => 'Catalogo_de_productos']);
        Permission::create(['name' => 'Editar catalogo de productos', 'category' => 'Catalogo_de_productos']);
        Permission::create(['name' => 'Eliminar catalogo de productos', 'category' => 'Catalogo_de_productos']);

        Permission::create(['name' => 'Ver cotizaciones','category' => 'Ventas']);
        Permission::create(['name' => 'Crear cotizaciones','category' => 'Ventas']);
        Permission::create(['name' => 'Editar cotizaciones','category' => 'Ventas']);
        Permission::create(['name' => 'Eliminar cotizaciones','category' => 'Ventas']);
        Permission::create(['name' => 'Autorizar cotizaciones','category' => 'Ventas']);

        Permission::create(['name' => 'Ver clientes', 'category' => 'Ventas']);
        Permission::create(['name' => 'Crear clientes', 'category' => 'Ventas']);
        Permission::create(['name' => 'Editar clientes', 'category' => 'Ventas']);
        Permission::create(['name' => 'Eliminar clientes', 'category' => 'Ventas']);

        Permission::create(['name' => 'Ver ordenes de venta', 'category' => 'Ventas']);
        Permission::create(['name' => 'Crear ordenes de venta', 'category' => 'Ventas']);
        Permission::create(['name' => 'Editar ordenes de venta', 'category' => 'Ventas']);
        Permission::create(['name' => 'Eliminar ordenes de venta', 'category' => 'Ventas']);
        Permission::create(['name' => 'Autorizar ordenes de venta', 'category' => 'Ventas']);

        Permission::create(['name' => 'Ver muestra', 'category' => 'Ventas']);
        Permission::create(['name' => 'Crear muestra', 'category' => 'Ventas']);
        Permission::create(['name' => 'Editar muestra', 'category' => 'Ventas']);
        Permission::create(['name' => 'Eliminar muestra', 'category' => 'Ventas']);
        Permission::create(['name' => 'Autorizar muestra', 'category' => 'Ventas']);
        Permission::create(['name' => 'Muestra devuelta', 'category' => 'Ventas']);
        Permission::create(['name' => 'Generar orden de venta en muestra', 'category' => 'Ventas']);

        Permission::create(['name' => 'Ver proveedores', 'category' => 'Compras']);
        Permission::create(['name' => 'Crear proveedores', 'category' => 'Compras']);
        Permission::create(['name' => 'Editar proveedores', 'category' => 'Compras']);
        Permission::create(['name' => 'Eliminar proveedores', 'category' => 'Compras']);

        Permission::create(['name' => 'Ver ordenes de compra', 'category' => 'Compras']);
        Permission::create(['name' => 'Cear ordenes de compra', 'category' => 'Compras']);
        Permission::create(['name' => 'Editar ordenes de compra', 'category' => 'Compras']);
        Permission::create(['name' => 'Eliminar ordenes de compra', 'category' => 'Compras']);
        Permission::create(['name' => 'Autorizar ordenes de compra', 'category' => 'Compras']);

        Permission::create(['name' => 'Ver materia prima', 'category' => 'Almacen']);
        Permission::create(['name' => 'Crear materia prima', 'category' => 'Almacen']);
        Permission::create(['name' => 'Editar materia prima', 'category' => 'Almacen']);
        Permission::create(['name' => 'Eliminar materia prima', 'category' => 'Almacen']);
        
        Permission::create(['name' => 'Ver insumos', 'category' => 'Almacen']);
        Permission::create(['name' => 'Crear insumos', 'category' => 'Almacen']);
        Permission::create(['name' => 'Editar insumos', 'category' => 'Almacen']);
        Permission::create(['name' => 'Eliminar insumos', 'category' => 'Almacen']);

        Permission::create(['name' => 'Ver producto terminado', 'category' => 'Almacen']);
        Permission::create(['name' => 'Crear producto terminado', 'category' => 'Almacen']);
        Permission::create(['name' => 'Editar producto terminado', 'category' => 'Almacen']);
        Permission::create(['name' => 'Eliminar producto terminado', 'category' => 'Almacen']);

        Permission::create(['name' => 'Ver scrap', 'category' => 'Almacen']);
        Permission::create(['name' => 'Crear scrap', 'category' => 'Almacen']);
        Permission::create(['name' => 'Editar scrap', 'category' => 'Almacen']);
        Permission::create(['name' => 'Eliminar scrap', 'category' => 'Almacen']);

        Permission::create(['name' => 'Crear entradas', 'category' => 'Almacen']);
        Permission::create(['name' => 'Crear salidas', 'category' => 'Almacen']);
        
        Permission::create(['name' => 'Generar reportes materia prima', 'category' => 'Almacen']);
        Permission::create(['name' => 'Generar reportes insumos', 'category' => 'Almacen']);
        
        Permission::create(['name' => 'Ver costo de almacen de producto terminado', 'category' => 'Almacen']);
        Permission::create(['name' => 'Ver costo de almacen de materia prima', 'category' => 'Almacen']);
        Permission::create(['name' => 'Ver costo de almacen de insumos', 'category' => 'Almacen']);
        Permission::create(['name' => 'Ver costo de almacen de scrap', 'category' => 'Almacen']);

        Permission::create(['name' => 'Ver nominas', 'category' => 'RRHH']);
        Permission::create(['name' => 'Crear nominas', 'category' => 'RRHH']);
        Permission::create(['name' => 'Editar nominas', 'category' => 'RRHH']);
        Permission::create(['name' => 'Eliminar nominas', 'category' => 'RRHH']);

        Permission::create(['name' => 'Ver solicitudes de tiempo adicional', 'category' => 'RRHH']);
        Permission::create(['name' => 'Crear solicitudes de tiempo adicional', 'category' => 'RRHH']);
        Permission::create(['name' => 'Editar solicitudes de tiempo adicional', 'category' => 'RRHH']);
        Permission::create(['name' => 'Eliminar solicitudes de tiempo adicional', 'category' => 'RRHH']);
        Permission::create(['name' => 'Autorizar solicitudes de tiempo adicional', 'category' => 'RRHH']);

        Permission::create(['name' => 'Ver personal', 'category' => 'RRHH']);
        Permission::create(['name' => 'Crear personal', 'category' => 'RRHH']);
        Permission::create(['name' => 'Editar personal', 'category' => 'RRHH']);
        Permission::create(['name' => 'Eliminar personal', 'category' => 'RRHH']);

        Permission::create(['name' => 'Ver roles y permisos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Crear roles y permisos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Editar roles y permisos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Eliminar roles y permisos', 'category' => 'RRHH']);

        Permission::create(['name' => 'Ver bonos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Crear bonos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Editar bonos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Eliminar bonos', 'category' => 'RRHH']);

        Permission::create(['name' => 'Ver descuentos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Crear descuentos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Editar descuentos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Eliminar descuentos', 'category' => 'RRHH']);

        Permission::create(['name' => 'Ver dias festivos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Crear dias festivos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Editar dias festivos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Eliminar dias festivos', 'category' => 'RRHH']);
        Permission::create(['name' => 'Crear kiosco', 'category' => 'RRHH']);

        Permission::create(['name' => 'Ver maquinas', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Crear maquinas', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Editar maquinas', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Eliminar maquinas', 'category' => 'Mantenimiento']);

        Permission::create(['name' => 'Ver mantenimientos', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Crear mantenimientos', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Editar mantenimientos', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Eliminar mantenimientos', 'category' => 'Mantenimiento']);

        Permission::create(['name' => 'Ver refacciones', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Crear refacciones', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Editar refacciones', 'category' => 'Mantenimiento']);
        Permission::create(['name' => 'Eliminar refacciones', 'category' => 'Mantenimiento']);

        Permission::create(['name' => 'Ver medios', 'category' => 'Generales']);
        Permission::create(['name' => 'Crear medios', 'category' => 'Generales']);
        Permission::create(['name' => 'Editar medios', 'category' => 'Generales']);
        Permission::create(['name' => 'Eliminar medios', 'category' => 'Generales']);
        Permission::create(['name' => 'Reuniones todas', 'category' => 'Generales']);
        Permission::create(['name' => 'Registrar asistencia', 'category' => 'Generales']);
        Permission::create(['name' => 'QR producto de catalogo', 'category' => 'Generales']);
        Permission::create(['name' => 'Chatear', 'category' => 'Generales']);
        Permission::create(['name' => 'Solicitudes de tiempo adicional personal', 'category' => 'Generales']);
        Permission::create(['name' => 'Reuniones personal', 'category' => 'Generales']);

        Permission::create(['name' => 'Ordenes de diseño todas', 'category' => 'Diseño']);
        Permission::create(['name' => 'Autorizar ordenes de diseño', 'category' => 'Diseño']);
        Permission::create(['name' => 'Ordenes de diseño personal', 'category' => 'Diseño']);

        Permission::create(['name' => 'Ordenes de produccion todas', 'category' => 'Produccion']);
        Permission::create(['name' => 'Autorizar ordenes de produccion', 'category' => 'Produccion']);
        Permission::create(['name' => 'Ordenes de produccion personal', 'category' => 'Produccion']);
        Permission::create(['name' => 'Ver costos de produccion', 'category' => 'Produccion']);
        Permission::create(['name' => 'Crear costos de produccion', 'category' => 'Produccion']);
        Permission::create(['name' => 'Editar costos de produccion', 'category' => 'Produccion']);
        Permission::create(['name' => 'Eliminar costos de produccion', 'category' => 'Produccion']);

        Permission::create(['name' => 'Ordenes de mercadotecnia todas', 'category' => 'Mercadotecnia']);
        Permission::create(['name' => 'Autorizar ordenes de mercadotecnia', 'category' => 'Mercadotecnia']);
        Permission::create(['name' => 'Ordenes de mercadotecnia personal', 'category' => 'Mercadotecnia']);


        // create roles and assign existing permissions
        Role::create(['name' => 'Auxiliar de produccion']);
        Role::create(['name' => 'Almacenista']);
        Role::create(['name' => 'Asistente de director']);
        Role::create(['name' => 'Diseñador']);
        Role::create(['name' => 'Inspector de calidad']);
        Role::create(['name' => 'Jefe de produccion']);
        Role::create(['name' => 'Mantenimiento']);
        Role::create(['name' => 'Mercadotecnia']);
        Role::create(['name' => 'Recursos humanos']);
        Role::create(['name' => 'Vendedor']);

        $role3 = Role::create(['name' => 'Super admin']);
        
        // gets all permissions
        $users = User::all();
        $users->each( fn ($user) => $user->assignRole($role3) );
    }
}
