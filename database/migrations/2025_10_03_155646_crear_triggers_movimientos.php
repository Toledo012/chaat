<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ================================
        // Usuarios
        // ================================
        DB::unprepared("
            CREATE TRIGGER trg_usuarios_insert
            AFTER INSERT ON Usuarios
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_nuevos, id_cuenta)
                VALUES ('Usuarios', 'INSERT', NEW.id_usuario,
                        JSON_OBJECT('nombre', NEW.nombre, 'email', NEW.email, 'departamento', NEW.departamento, 'puesto', NEW.puesto),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_usuarios_update
            AFTER UPDATE ON Usuarios
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, datos_nuevos, id_cuenta)
                VALUES ('Usuarios', 'UPDATE', NEW.id_usuario,
                        JSON_OBJECT('nombre', OLD.nombre, 'email', OLD.email, 'departamento', OLD.departamento, 'puesto', OLD.puesto),
                        JSON_OBJECT('nombre', NEW.nombre, 'email', NEW.email, 'departamento', NEW.departamento, 'puesto', NEW.puesto),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_usuarios_delete
            AFTER DELETE ON Usuarios
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, id_cuenta)
                VALUES ('Usuarios', 'DELETE', OLD.id_usuario,
                        JSON_OBJECT('nombre', OLD.nombre, 'email', OLD.email, 'departamento', OLD.departamento, 'puesto', OLD.puesto),
                        @id_cuenta);
            END;
        ");

        // ================================
        // Cuentas
        // ================================
        DB::unprepared("
            CREATE TRIGGER trg_cuentas_insert
            AFTER INSERT ON Cuentas
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_nuevos, id_cuenta)
                VALUES ('Cuentas', 'INSERT', NEW.id_cuenta,
                        JSON_OBJECT('username', NEW.username, 'estado', NEW.estado, 'rol', NEW.id_rol),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_cuentas_update
            AFTER UPDATE ON Cuentas
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, datos_nuevos, id_cuenta)
                VALUES ('Cuentas', 'UPDATE', NEW.id_cuenta,
                        JSON_OBJECT('username', OLD.username, 'estado', OLD.estado, 'rol', OLD.id_rol),
                        JSON_OBJECT('username', NEW.username, 'estado', NEW.estado, 'rol', NEW.id_rol),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_cuentas_delete
            AFTER DELETE ON Cuentas
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, id_cuenta)
                VALUES ('Cuentas', 'DELETE', OLD.id_cuenta,
                        JSON_OBJECT('username', OLD.username, 'estado', OLD.estado, 'rol', OLD.id_rol),
                        @id_cuenta);
            END;
        ");

        // ================================
        // Servicios
        // ================================
        DB::unprepared("
            CREATE TRIGGER trg_servicios_insert
            AFTER INSERT ON Servicios
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_nuevos, id_cuenta)
                VALUES ('Servicios', 'INSERT', NEW.id_servicio,
                        JSON_OBJECT('folio', NEW.folio, 'fecha', NEW.fecha, 'tipo_formato', NEW.tipo_formato),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_servicios_update
            AFTER UPDATE ON Servicios
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, datos_nuevos, id_cuenta)
                VALUES ('Servicios', 'UPDATE', NEW.id_servicio,
                        JSON_OBJECT('folio', OLD.folio, 'fecha', OLD.fecha, 'tipo_formato', OLD.tipo_formato),
                        JSON_OBJECT('folio', NEW.folio, 'fecha', NEW.fecha, 'tipo_formato', NEW.tipo_formato),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_servicios_delete
            AFTER DELETE ON Servicios
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, id_cuenta)
                VALUES ('Servicios', 'DELETE', OLD.id_servicio,
                        JSON_OBJECT('folio', OLD.folio, 'fecha', OLD.fecha, 'tipo_formato', OLD.tipo_formato),
                        @id_cuenta);
            END;
        ");

        // ================================
        // Materiales_Utilizados
        // ================================
        DB::unprepared("
            CREATE TRIGGER trg_materiales_insert
            AFTER INSERT ON Materiales_Utilizados
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_nuevos, id_cuenta)
                VALUES ('Materiales_Utilizados', 'INSERT', NEW.id_relacion,
                        JSON_OBJECT('id_servicio', NEW.id_servicio, 'id_material', NEW.id_material, 'cantidad', NEW.cantidad, 'costo', NEW.costo_aproximado),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_materiales_update
            AFTER UPDATE ON Materiales_Utilizados
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, datos_nuevos, id_cuenta)
                VALUES ('Materiales_Utilizados', 'UPDATE', NEW.id_relacion,
                        JSON_OBJECT('id_servicio', OLD.id_servicio, 'id_material', OLD.id_material, 'cantidad', OLD.cantidad, 'costo', OLD.costo_aproximado),
                        JSON_OBJECT('id_servicio', NEW.id_servicio, 'id_material', NEW.id_material, 'cantidad', NEW.cantidad, 'costo', NEW.costo_aproximado),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_materiales_delete
            AFTER DELETE ON Materiales_Utilizados
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, id_cuenta)
                VALUES ('Materiales_Utilizados', 'DELETE', OLD.id_relacion,
                        JSON_OBJECT('id_servicio', OLD.id_servicio, 'id_material', OLD.id_material, 'cantidad', OLD.cantidad, 'costo', OLD.costo_aproximado),
                        @id_cuenta);
            END;
        ");

        // ================================
        // Formato_A
        // ================================
        DB::unprepared("
            CREATE TRIGGER trg_formatoA_insert
            AFTER INSERT ON Formato_A
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_nuevos, id_cuenta)
                VALUES ('Formato_A', 'INSERT', NEW.id_formatoA,
                        JSON_OBJECT('subtipo', NEW.subtipo, 'tipo_atencion', NEW.tipo_atencion, 'tipo_servicio', NEW.tipo_servicio, 'conclusion', NEW.conclusion_servicio),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_formatoA_update
            AFTER UPDATE ON Formato_A
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, datos_nuevos, id_cuenta)
                VALUES ('Formato_A', 'UPDATE', NEW.id_formatoA,
                        JSON_OBJECT('subtipo', OLD.subtipo, 'tipo_atencion', OLD.tipo_atencion, 'tipo_servicio', OLD.tipo_servicio, 'conclusion', OLD.conclusion_servicio),
                        JSON_OBJECT('subtipo', NEW.subtipo, 'tipo_atencion', NEW.tipo_atencion, 'tipo_servicio', NEW.tipo_servicio, 'conclusion', NEW.conclusion_servicio),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_formatoA_delete
            AFTER DELETE ON Formato_A
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, id_cuenta)
                VALUES ('Formato_A', 'DELETE', OLD.id_formatoA,
                        JSON_OBJECT('subtipo', OLD.subtipo, 'tipo_atencion', OLD.tipo_atencion, 'tipo_servicio', OLD.tipo_servicio, 'conclusion', OLD.conclusion_servicio),
                        @id_cuenta);
            END;
        ");

        // ================================
        // Formato_B
        // ================================
        DB::unprepared("
            CREATE TRIGGER trg_formatoB_insert
            AFTER INSERT ON Formato_B
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_nuevos, id_cuenta)
                VALUES ('Formato_B', 'INSERT', NEW.id_formatoB,
                        JSON_OBJECT('subtipo', NEW.subtipo, 'equipo', NEW.equipo, 'marca', NEW.marca, 'tipo_servicio', NEW.tipo_servicio, 'diagnostico', NEW.diagnostico),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_formatoB_update
            AFTER UPDATE ON Formato_B
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, datos_nuevos, id_cuenta)
                VALUES ('Formato_B', 'UPDATE', NEW.id_formatoB,
                        JSON_OBJECT('subtipo', OLD.subtipo, 'equipo', OLD.equipo, 'marca', OLD.marca, 'tipo_servicio', OLD.tipo_servicio, 'diagnostico', OLD.diagnostico),
                        JSON_OBJECT('subtipo', NEW.subtipo, 'equipo', NEW.equipo, 'marca', NEW.marca, 'tipo_servicio', NEW.tipo_servicio, 'diagnostico', NEW.diagnostico),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_formatoB_delete
            AFTER DELETE ON Formato_B
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, id_cuenta)
                VALUES ('Formato_B', 'DELETE', OLD.id_formatoB,
                        JSON_OBJECT('subtipo', OLD.subtipo, 'equipo', OLD.equipo, 'marca', OLD.marca, 'tipo_servicio', OLD.tipo_servicio, 'diagnostico', OLD.diagnostico),
                        @id_cuenta);
            END;
        ");

        // ================================
        // Formato_C
        // ================================
        DB::unprepared("
            CREATE TRIGGER trg_formatoC_insert
            AFTER INSERT ON Formato_C
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_nuevos, id_cuenta)
                VALUES ('Formato_C', 'INSERT', NEW.id_formatoC,
                        JSON_OBJECT('tipo_red', NEW.tipo_red, 'tipo_servicio', NEW.tipo_servicio, 'diagnostico', NEW.diagnostico),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_formatoC_update
            AFTER UPDATE ON Formato_C
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, datos_nuevos, id_cuenta)
                VALUES ('Formato_C', 'UPDATE', NEW.id_formatoC,
                        JSON_OBJECT('tipo_red', OLD.tipo_red, 'tipo_servicio', OLD.tipo_servicio, 'diagnostico', OLD.diagnostico),
                        JSON_OBJECT('tipo_red', NEW.tipo_red, 'tipo_servicio', NEW.tipo_servicio, 'diagnostico', NEW.diagnostico),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_formatoC_delete
            AFTER DELETE ON Formato_C
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, id_cuenta)
                VALUES ('Formato_C', 'DELETE', OLD.id_formatoC,
                        JSON_OBJECT('tipo_red', OLD.tipo_red, 'tipo_servicio', OLD.tipo_servicio, 'diagnostico', OLD.diagnostico),
                        @id_cuenta);
            END;
        ");

        // ================================
        // Formato_D
        // ================================
        DB::unprepared("
            CREATE TRIGGER trg_formatoD_insert
            AFTER INSERT ON Formato_D
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_nuevos, id_cuenta)
                VALUES ('Formato_D', 'INSERT', NEW.id_formatoD,
                        JSON_OBJECT('equipo', NEW.equipo, 'marca', NEW.marca, 'modelo', NEW.modelo, 'serie', NEW.serie),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_formatoD_update
            AFTER UPDATE ON Formato_D
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, datos_nuevos, id_cuenta)
                VALUES ('Formato_D', 'UPDATE', NEW.id_formatoD,
                        JSON_OBJECT('equipo', OLD.equipo, 'marca', OLD.marca, 'modelo', OLD.modelo, 'serie', OLD.serie),
                        JSON_OBJECT('equipo', NEW.equipo, 'marca', NEW.marca, 'modelo', NEW.modelo, 'serie', NEW.serie),
                        @id_cuenta);
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_formatoD_delete
            AFTER DELETE ON Formato_D
            FOR EACH ROW
            BEGIN
                INSERT INTO Movimientos (tabla, accion, id_registro, datos_anteriores, id_cuenta)
                VALUES ('Formato_D', 'DELETE', OLD.id_formatoD,
                        JSON_OBJECT('equipo', OLD.equipo, 'marca', OLD.marca, 'modelo', OLD.modelo, 'serie', OLD.serie),
                        @id_cuenta);
            END;
        ");
    }

    public function down(): void
    {
        // ================================
        // Drop todos los triggers
        // ================================
        $triggers = [
            'trg_usuarios_insert','trg_usuarios_update','trg_usuarios_delete',
            'trg_cuentas_insert','trg_cuentas_update','trg_cuentas_delete',
            'trg_servicios_insert','trg_servicios_update','trg_servicios_delete',
            'trg_materiales_insert','trg_materiales_update','trg_materiales_delete',
            'trg_formatoA_insert','trg_formatoA_update','trg_formatoA_delete',
            'trg_formatoB_insert','trg_formatoB_update','trg_formatoB_delete',
            'trg_formatoC_insert','trg_formatoC_update','trg_formatoC_delete',
            'trg_formatoD_insert','trg_formatoD_update','trg_formatoD_delete',
        ];

        foreach ($triggers as $trigger) {
            DB::unprepared("DROP TRIGGER IF EXISTS $trigger;");
        }
    }
};
