<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────
        // usuario (id_cocina_negocio_asociado se enlaza
        // a negocios más abajo, después de crear esa tabla)
        // ─────────────────────────────────────────────
        Schema::create('usuario', function (Blueprint $table) {
            $table->unsignedInteger('id_usuario')->primary();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('direccion', 255)->nullable();
            $table->string('email', 150)->unique('uq_usuario_email');
            $table->string('password_hash', 255);
            $table->timestamp('fecha_registro')->useCurrent();
            $table->string('rol', 20);
            $table->string('ciudad', 100)->nullable()->default('Chapinero');
            $table->unsignedInteger('id_cocina_negocio_asociado')->nullable();
        });

        // ─────────────────────────────────────────────
        // categorias
        // ─────────────────────────────────────────────
        Schema::create('categorias', function (Blueprint $table) {
            $table->increments('id_categoria');
            $table->string('nombre', 100);
            $table->string('icon', 10);
            $table->text('descripcion')->nullable();
            $table->string('cover_img', 255)->nullable();
            $table->string('bg_color', 20)->nullable();
            $table->string('accent_color', 20)->nullable();
        });

        // ─────────────────────────────────────────────
        // negocios
        // ─────────────────────────────────────────────
        Schema::create('negocios', function (Blueprint $table) {
            $table->increments('id_negocio');
            $table->unsignedInteger('id_usuario')->unique('uq_negocios_usuario');
            $table->text('gmail_negocio');
            $table->string('nombre', 100);
            $table->string('direccion', 255);
            $table->string('cedula', 20);
            $table->time('hora_apertura');
            $table->time('hora_cierre');
            $table->string('logo_url', 500)->nullable();
            $table->string('rut_url', 500);
            $table->string('documento_identidad_representante_url', 500);
            $table->string('certificado_bancario_url', 500);
            $table->string('certificado_camara_comercio_url', 500)->nullable();

            $table->foreign('id_usuario', 'fk_negocios_usuario')
                ->references('id_usuario')->on('usuario');
        });

        // FK pendiente de usuario -> negocios (id_cocina_negocio_asociado)
        Schema::table('usuario', function (Blueprint $table) {
            $table->foreign('id_cocina_negocio_asociado', 'fk_usuario_cocina_negocio')
                ->references('id_negocio')->on('negocios');
        });

        // ─────────────────────────────────────────────
        // menu_items
        // ─────────────────────────────────────────────
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id_menu_item');
            $table->unsignedInteger('id_negocio')->nullable();
            $table->string('nombre', 100)->nullable();
            $table->text('descripcion');
            $table->integer('precio');
            $table->integer('stock')->default(0);
            $table->string('imagen_url', 255)->nullable();
            $table->unsignedInteger('id_categoria')->nullable();
            $table->boolean('on_promo')->default(0);
            $table->integer('precio_promocion')->nullable();

            $table->unique(['id_menu_item', 'id_negocio'], 'uq_menuitem_id_negocio');
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
            $table->foreign('id_categoria', 'fk_menu_categoria')
                ->references('id_categoria')->on('categorias')->nullOnDelete();
        });

        // ─────────────────────────────────────────────
        // pedido
        // ─────────────────────────────────────────────
        Schema::create('pedido', function (Blueprint $table) {
            $table->unsignedInteger('id_pedido')->primary();
            $table->unsignedInteger('id_usuario');
            $table->unsignedInteger('id_negocio');
            $table->text('descripcion')->nullable();
            $table->string('direccion_entrega', 255);
            $table->string('estado', 20)->default('Pendiente');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->foreign('id_usuario', 'fk_pedido_usuario')
                ->references('id_usuario')->on('usuario');
            $table->foreign('id_negocio', 'fk_pedido_negocio')
                ->references('id_negocio')->on('negocios');
        });

        // ─────────────────────────────────────────────
        // compra
        // ─────────────────────────────────────────────
        Schema::create('compra', function (Blueprint $table) {
            $table->unsignedInteger('id_compra')->primary();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->unsignedInteger('id_pedido')->unique('uq_compra_pedido');
            $table->timestamp('fecha_pago')->useCurrent();
            $table->integer('total');
            $table->string('metodo_pago', 30);
            $table->string('estado', 20)->default('Pendiente');
            $table->string('token_transaccion', 100)->unique('uq_compra_token');

            $table->foreign('id_pedido', 'fk_compra_pedido')
                ->references('id_pedido')->on('pedido');
            $table->foreign('id_usuario', 'fk_compra_usuario')
                ->references('id_usuario')->on('usuario');
        });

        // ─────────────────────────────────────────────
        // detalle_pedido
        // ─────────────────────────────────────────────
        Schema::create('detalle_pedido', function (Blueprint $table) {
            $table->increments('id_detalle');
            $table->unsignedInteger('id_pedido');
            $table->unsignedInteger('id_menu_item');
            $table->integer('valor');
            $table->timestamp('fecha')->useCurrent();
            $table->integer('cantidad')->default(1);

            $table->foreign('id_pedido', 'fk_detalle_pedido_id')
                ->references('id_pedido')->on('pedido');
            $table->foreign('id_menu_item', 'fk_detalle_pedido_menu_items')
                ->references('id_menu_item')->on('menu_items');
        });

        // ─────────────────────────────────────────────
        // repartidor (debe existir antes que entrega,
        // que tiene FK hacia esta tabla)
        // ─────────────────────────────────────────────
        Schema::create('repartidor', function (Blueprint $table) {
            $table->increments('id_repartidor');
            $table->unsignedInteger('id_usuario')->unique('uq_repartidor_usuario');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email_repartidor', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('cedula', 20);
            $table->string('vehiculo', 50);
            $table->string('foto_url', 500);
            $table->string('cedula_documento_url', 500);
            $table->string('licencia_conduccion_url', 500)->nullable();
            $table->string('soat_url', 500)->nullable();
            $table->string('tarjeta_propiedad_url', 500)->nullable();
            $table->string('estado', 15)->default('Activo');

            $table->foreign('id_usuario', 'fk_repartidor_usuario')
                ->references('id_usuario')->on('usuario');
        });

        // ─────────────────────────────────────────────
        // entrega
        // ─────────────────────────────────────────────
        Schema::create('entrega', function (Blueprint $table) {
            $table->increments('id_entrega');
            $table->unsignedInteger('id_compra')->unique('uq_entrega_compra');
            $table->unsignedInteger('id_repartidor')->nullable();
            $table->string('estado', 20)->default('Pendiente');
            $table->string('codigo_entrega', 10)->nullable();
            $table->dateTime('fecha_asignacion')->nullable();
            $table->dateTime('fecha_entrega')->nullable();
            $table->dateTime('fecha_confirmacion')->nullable();

            $table->foreign('id_compra', 'fk_entrega_compra')
                ->references('id_compra')->on('compra');
            $table->foreign('id_repartidor', 'fk_entrega_repartidor')
                ->references('id_repartidor')->on('repartidor');
        });

        // ─────────────────────────────────────────────
        // favorito
        // ─────────────────────────────────────────────
        Schema::create('favorito', function (Blueprint $table) {
            $table->increments('id_favorito');
            $table->unsignedInteger('id_negocio')->nullable();
            $table->unsignedInteger('id_menu_item')->nullable();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->date('fecha_reg')->default(DB::raw('(CURRENT_DATE)'));

            $table->unique(['id_usuario', 'id_negocio'], 'uq_favorito_usuario_negocio');
            $table->unique(['id_usuario', 'id_menu_item'], 'uq_favorito_usuario_menu_item');
            $table->foreign('id_negocio', 'fk_favorito_negocios')
                ->references('id_negocio')->on('negocios');
            $table->foreign('id_menu_item', 'fk_favorito_menu_item')
                ->references('id_menu_item')->on('menu_items')->nullOnDelete();
            $table->foreign('id_usuario', 'fk_favorito_usuario')
                ->references('id_usuario')->on('usuario');
        });

        // ─────────────────────────────────────────────
        // calificacion
        // ─────────────────────────────────────────────
        Schema::create('calificacion', function (Blueprint $table) {
            $table->increments('id_calificacion');
            $table->unsignedInteger('id_negocio')->nullable();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->unsignedInteger('id_repartidor')->nullable();
            $table->unsignedInteger('id_pedido')->nullable();
            $table->string('comentario', 500)->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->tinyInteger('puntuacion')->default(5);

            $table->unique(['id_usuario', 'id_negocio'], 'uq_calificacion_usuario_negocio');
            $table->foreign('id_negocio', 'fk_calificacion_negocios')
                ->references('id_negocio')->on('negocios');
            $table->foreign('id_usuario', 'fk_calificacion_usuario')
                ->references('id_usuario')->on('usuario');
            $table->foreign('id_repartidor', 'fk_calificacion_repartidor')
                ->references('id_repartidor')->on('repartidor')->nullOnDelete();
            $table->foreign('id_pedido', 'fk_calificacion_pedido')
                ->references('id_pedido')->on('pedido')->nullOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE calificacion ADD CONSTRAINT chk_calificacion_puntuacion CHECK (puntuacion BETWEEN 1 AND 5)');
        }

        // ─────────────────────────────────────────────
        // calificacion_repartidor
        // ─────────────────────────────────────────────
        Schema::create('calificacion_repartidor', function (Blueprint $table) {
            $table->increments('id_calificacion_repartidor');
            $table->unsignedInteger('id_repartidor');
            $table->unsignedInteger('id_usuario');
            $table->unsignedInteger('id_pedido')->unique('uq_calificacion_repartidor_pedido');
            $table->string('comentario', 500)->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->tinyInteger('puntuacion')->default(5);

            $table->foreign('id_repartidor', 'fk_cr_repartidor')
                ->references('id_repartidor')->on('repartidor');
            $table->foreign('id_usuario', 'fk_cr_usuario')
                ->references('id_usuario')->on('usuario');
            $table->foreign('id_pedido', 'fk_cr_pedido')
                ->references('id_pedido')->on('pedido');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE calificacion_repartidor ADD CONSTRAINT chk_cr_puntuacion CHECK (puntuacion BETWEEN 1 AND 5)');
        }

        // ─────────────────────────────────────────────
        // promociones
        // ─────────────────────────────────────────────
        Schema::create('promociones', function (Blueprint $table) {
            $table->increments('id_promocion');
            $table->unsignedInteger('id_negocio')->nullable();
            $table->unsignedInteger('id_menu_item')->nullable();
            $table->string('nombre', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen_url', 255)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(1);

            $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
            $table->foreign(['id_menu_item', 'id_negocio'], 'fk_promociones_menu_item')
                ->references(['id_menu_item', 'id_negocio'])->on('menu_items')
                ->cascadeOnDelete();
        });

        // ─────────────────────────────────────────────
        // log_compra
        // ─────────────────────────────────────────────
        Schema::create('log_compra', function (Blueprint $table) {
            $table->increments('id_log');
            $table->unsignedInteger('id_compra');
            $table->unsignedInteger('id_usuario');
            $table->integer('total');
            $table->string('metodo_pago', 30);
            $table->string('estado', 20);
            $table->timestamp('fecha_registro')->useCurrent();
            $table->string('accion', 10);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('
                CREATE TRIGGER trg_descontar_stock
                AFTER INSERT ON detalle_pedido
                FOR EACH ROW
                BEGIN
                    DECLARE stock_actual INT;
                    SELECT stock INTO stock_actual FROM menu_items WHERE id_menu_item = NEW.id_menu_item;
                    IF stock_actual < NEW.cantidad THEN
                        SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Stock insuficiente para uno o mas articulos del pedido.";
                    ELSE
                        UPDATE menu_items SET stock = stock - NEW.cantidad WHERE id_menu_item = NEW.id_menu_item;
                    END IF;
                END
            ');

            DB::unprepared('
                CREATE TRIGGER trg_restaurar_stock
                AFTER DELETE ON detalle_pedido
                FOR EACH ROW
                BEGIN
                    UPDATE menu_items SET stock = stock + OLD.cantidad WHERE id_menu_item = OLD.id_menu_item;
                END
            ');

            DB::unprepared('
                CREATE TRIGGER trg_log_compra_insert
                AFTER INSERT ON compra
                FOR EACH ROW
                BEGIN
                    INSERT INTO log_compra (id_compra, id_usuario, total, metodo_pago, estado, fecha_registro, accion)
                    VALUES (NEW.id_compra, NEW.id_usuario, NEW.total, NEW.metodo_pago, NEW.estado, NOW(), "INSERT");
                END
            ');

            DB::unprepared('
                CREATE TRIGGER trg_log_compra_update
                AFTER UPDATE ON compra
                FOR EACH ROW
                BEGIN
                    INSERT INTO log_compra (id_compra, id_usuario, total, metodo_pago, estado, fecha_registro, accion)
                    VALUES (NEW.id_compra, NEW.id_usuario, NEW.total, NEW.metodo_pago, NEW.estado, NOW(), "UPDATE");
                END
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS trg_log_compra_update');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_log_compra_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_restaurar_stock');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_descontar_stock');
        }
        Schema::dropIfExists('log_compra');
        Schema::dropIfExists('promociones');
        Schema::dropIfExists('calificacion_repartidor');
        Schema::dropIfExists('calificacion');
        Schema::dropIfExists('favorito');
        Schema::dropIfExists('entrega');
        Schema::dropIfExists('repartidor');
        Schema::dropIfExists('detalle_pedido');
        Schema::dropIfExists('compra');
        Schema::dropIfExists('pedido');
        Schema::dropIfExists('menu_items');

        Schema::table('usuario', function (Blueprint $table) {
            $table->dropForeign('fk_usuario_cocina_negocio');
        });

        Schema::dropIfExists('negocios');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('usuario');
    }
};
