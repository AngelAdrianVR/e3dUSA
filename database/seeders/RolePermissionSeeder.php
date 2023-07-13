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
        Permission::create(['name' => 'Ver catalogo de productos', 'guard_name' => 'Catalogo_de_productos']);
        Permission::create(['name' => 'Crear catalogo de productos', 'guard_name' => 'Catalogo_de_productos']);
        Permission::create(['name' => 'Editar catalogo de productos', 'guard_name' => 'Catalogo_de_productos']);
        Permission::create(['name' => 'Eliminar catalogo de productos', 'guard_name' => 'Catalogo_de_productos']);

        Permission::create(['name' => 'Ver cotizaciones','guard_name' => 'Ventas']);
        Permission::create(['name' => 'Crear cotizaciones','guard_name' => 'Ventas']);
        Permission::create(['name' => 'Editar cotizaciones','guard_name' => 'Ventas']);
        Permission::create(['name' => 'Eliminar cotizaciones','guard_name' => 'Ventas']);
        Permission::create(['name' => 'Autorizar cotizaciones','guard_name' => 'Ventas']);

        Permission::create(['name' => 'Ver clientes', 'guard_name' => 'Ventas']);
        Permission::create(['name' => 'Crear clientes', 'guard_name' => 'Ventas']);
        Permission::create(['name' => 'Editar clientes', 'guard_name' => 'Ventas']);
        Permission::create(['name' => 'Eliminar clientes', 'guard_name' => 'Ventas']);

        Permission::create(['name' => 'Ver ordenes de venta', 'guard_name' => 'Ventas']);
        Permission::create(['name' => 'Crear ordenes de venta', 'guard_name' => 'Ventas']);
        Permission::create(['name' => 'Editar ordenes de venta', 'guard_name' => 'Ventas']);
        Permission::create(['name' => 'Eliminar ordenes de venta', 'guard_name' => 'Ventas']);
        Permission::create(['name' => 'Autorizar ordenes de venta', 'guard_name' => 'Ventas']);

        Permission::create(['name' => 'Ver proveedores', 'guard_name' => 'Compras']);
        Permission::create(['name' => 'Crear proveedores', 'guard_name' => 'Compras']);
        Permission::create(['name' => 'Editar proveedores', 'guard_name' => 'Compras']);
        Permission::create(['name' => 'Eliminar proveedores', 'guard_name' => 'Compras']);

        Permission::create(['name' => 'Ver ordenes de compra', 'guard_name' => 'Compras']);
        Permission::create(['name' => 'Cear ordenes de compra', 'guard_name' => 'Compras']);
        Permission::create(['name' => 'Editar ordenes de compra', 'guard_name' => 'Compras']);
        Permission::create(['name' => 'Eliminar ordenes de compra', 'guard_name' => 'Compras']);
        Permission::create(['name' => 'Autorizar ordenes de compra', 'guard_name' => 'Compras']);

        Permission::create(['name' => 'Ver materia prima', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Crear materia prima', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Editar materia prima', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Eliminar materia prima', 'guard_name' => 'Almacen']);
        
        Permission::create(['name' => 'Ver insumos', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Crear insumos', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Editar insumos', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Eliminar insumos', 'guard_name' => 'Almacen']);

        Permission::create(['name' => 'Ver producto terminado', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Crear producto terminado', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Editar producto terminado', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Eliminar producto terminado', 'guard_name' => 'Almacen']);

        Permission::create(['name' => 'Ver scrap', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Crear scrap', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Editar scrap', 'guard_name' => 'Almacen']);
        Permission::create(['name' => 'Eliminar scrap', 'guard_name' => 'Almacen']);

        Permission::create(['name' => 'Ver nominas', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Crear nominas', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Editar nominas', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Eliminar nominas', 'guard_name' => 'RRHH']);

        Permission::create(['name' => 'Ver solicitudes de tiempo adicional', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Crear solicitudes de tiempo adicional', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Editar solicitudes de tiempo adicional', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Eliminar solicitudes de tiempo adicional', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Autorizar solicitudes de tiempo adicional', 'guard_name' => 'RRHH']);

        Permission::create(['name' => 'Ver personal', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Crear personal', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Editar personal', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Eliminar personal', 'guard_name' => 'RRHH']);

        Permission::create(['name' => 'Ver roles y permisos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Crear roles y permisos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Editar roles y permisos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Eliminar roles y permisos', 'guard_name' => 'RRHH']);

        Permission::create(['name' => 'Ver bonos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Crear bonos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Editar bonos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Eliminar bonos', 'guard_name' => 'RRHH']);

        Permission::create(['name' => 'Ver dias festivos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Crear dias festivos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Editar dias festivos', 'guard_name' => 'RRHH']);
        Permission::create(['name' => 'Eliminar dias festivos', 'guard_name' => 'RRHH']);

        Permission::create(['name' => 'Ver maquinas', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Crear maquinas', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Editar maquinas', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Eliminar maquinas', 'guard_name' => 'Mantenimiento']);

        Permission::create(['name' => 'Ver mantenimientos', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Crear mantenimientos', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Editar mantenimientos', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Eliminar mantenimientos', 'guard_name' => 'Mantenimiento']);

        Permission::create(['name' => 'Ver refacciones', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Crear refacciones', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Editar refacciones', 'guard_name' => 'Mantenimiento']);
        Permission::create(['name' => 'Eliminar refacciones', 'guard_name' => 'Mantenimiento']);

        Permission::create(['name' => 'Ver medios', 'guard_name' => 'Generales']);
        Permission::create(['name' => 'Crear medios', 'guard_name' => 'Generales']);
        Permission::create(['name' => 'Editar medios', 'guard_name' => 'Generales']);
        Permission::create(['name' => 'Eliminar medios', 'guard_name' => 'Generales']);
        Permission::create(['name' => 'Reuniones todas', 'guard_name' => 'Generales']);
        Permission::create(['name' => 'Registrar asistencia', 'guard_name' => 'Generales']);
        Permission::create(['name' => 'Chatear', 'guard_name' => 'Generales']);
        Permission::create(['name' => 'Solicitudes de tiempo adicional personal', 'guard_name' => 'Generales']);
        Permission::create(['name' => 'Reuniones personal', 'guard_name' => 'Generales']);

        Permission::create(['name' => 'Ordenes de diseño todas', 'guard_name' => 'Diseño']);
        Permission::create(['name' => 'Autorizar ordenes de diseño', 'guard_name' => 'Diseño']);
        Permission::create(['name' => 'Ordenes de diseño personal', 'guard_name' => 'Diseño']);

        Permission::create(['name' => 'Ordenes de produccion todas', 'guard_name' => 'Produccion']);
        Permission::create(['name' => 'Autorizar ordenes de produccion', 'guard_name' => 'Produccion']);
        Permission::create(['name' => 'Ordenes de produccion personal', 'guard_name' => 'Produccion']);

        Permission::create(['name' => 'Ordenes de mercadotecnia todas', 'guard_name' => 'Mercadotecnia']);
        Permission::create(['name' => 'Autorizar ordenes de mercadotecnia', 'guard_name' => 'Mercadotecnia']);
        Permission::create(['name' => 'Ordenes de mercadotecnia personal', 'guard_name' => 'Mercadotecnia']);


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
        
        // gets all permissions via Gate::before rule; see AuthServiceProvider
        $users = User::all();
        $users->each( fn ($user) => $user->assignRole($role3) );
    }
}
