<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller

{
    public function showRegistroUsuario()
    {
        return view('registro_usuario');
    }
    public function showRegistroNegocio()
    {
        return view('registro_negocio');
    }
    public function showRegistroRepartidor()
    {
        return view('registro_repartidor');
    }

    public function showDocumentosNegocio(Request $request)
    {
        return $request->session()->has('registro_negocio')
            ? view('documentos_negocio')
            : redirect()->route('registro.negocio');
    }

    public function showDocumentosRepartidor(Request $request)
    {
        return $request->session()->has('registro_repartidor')
            ? view('documentos_repartidor')
            : redirect()->route('registro.repartidor');
    }

    public function continueRegistroNegocio(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:150'],
            'direccion' => ['required', 'string', 'min:5', 'max:150'],
            'hora_apertura' => ['required', 'date_format:H:i'],
            'hora_cierre' => ['required', 'date_format:H:i'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terminos' => ['required', 'in:aceptado'],
        ]);

        $request->session()->put('registro_negocio', $validated);

        return view('documentos_negocio');
    }

    public function continueRegistroRepartidor(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
            'apellido' => ['required', 'string', 'min:2', 'max:100'],
            'vehiculo' => ['required', 'in:Bicicleta,Moto,Patineta electrica,Carro'],
            'direccion' => ['required', 'string', 'min:5', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terminos' => ['required', 'in:aceptado'],
        ]);

        $request->session()->put('registro_repartidor', $validated);

        return view('documentos_repartidor');
    }

    public function finishRegistroNegocio(Request $request)
    {
        $registro = $request->session()->get('registro_negocio');
        abort_unless($registro, 419, 'La sesión del registro expiró.');

        $validated = $request->validate([
            'cedula' => ['required', 'digits_between:6,12'],
            'logo_negocio' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'rut' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'documento_identidad_representante' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'certificado_bancario' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'certificado_camara_comercio' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        DB::transaction(function () use ($registro, $validated, $request): void {
            $idUsuario = $this->createUsuario($registro, 'Negocio');
            $slug = Str::slug($registro['nombre']) . '-' . Str::random(8);

            DB::table('negocios')->insert([
                'id_usuario' => $idUsuario,
                'gmail_negocio' => $registro['email'],
                'nombre' => $registro['nombre'],
                'direccion' => $registro['direccion'],
                'cedula' => $validated['cedula'],
                'hora_apertura' => $registro['hora_apertura'],
                'hora_cierre' => $registro['hora_cierre'],
                'logo_url' => $this->storeUpload($request->file('logo_negocio'), 'negocios', $slug),
                'rut_url' => $this->storeUpload($request->file('rut'), 'negocios', $slug),
                'documento_identidad_representante_url' => $this->storeUpload($request->file('documento_identidad_representante'), 'negocios', $slug),
                'certificado_bancario_url' => $this->storeUpload($request->file('certificado_bancario'), 'negocios', $slug),
                'certificado_camara_comercio_url' => $request->hasFile('certificado_camara_comercio')
                    ? $this->storeUpload($request->file('certificado_camara_comercio'), 'negocios', $slug)
                    : null,
            ]);
        });

        $request->session()->forget('registro_negocio');

        return redirect()->route('registro.exitoso');
    }

    public function finishRegistroRepartidor(Request $request)
    {
        $registro = $request->session()->get('registro_repartidor');
        abort_unless($registro, 419, 'La sesión del registro expiró.');

        $validated = $request->validate([
            'cedula' => ['required', 'digits_between:6,12'],
            'foto_repartidor' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'documento_cedula' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        DB::transaction(function () use ($registro, $validated, $request): void {
            $idUsuario = $this->createUsuario($registro, 'Repartidor');
            $slug = Str::slug($registro['nombre'] . '-' . $registro['apellido']) . '-' . Str::random(8);

            DB::table('repartidor')->insert([
                'id_usuario' => $idUsuario,
                'nombre' => $registro['nombre'],
                'apellido' => $registro['apellido'],
                'email_repartidor' => $registro['email'],
                'direccion' => $registro['direccion'],
                'cedula' => $validated['cedula'],
                'vehiculo' => $registro['vehiculo'],
                'foto_url' => $this->storeUpload($request->file('foto_repartidor'), 'repartidores', $slug),
                'cedula_documento_url' => $this->storeUpload($request->file('documento_cedula'), 'repartidores', $slug),
                'estado' => 'Pendiente',
            ]);
        });

        $request->session()->forget('registro_repartidor');

        return redirect()->route('registro.exitoso');
    }

    private function createUsuario(array $registro, string $rol): int
    {
        $idUsuario = ((int) DB::table('usuario')->lockForUpdate()->max('id_usuario')) + 1;

        $user = [
            'id_usuario' => $idUsuario,
            'nombre' => $registro['nombre'],
            'apellido' => $registro['apellido'] ?? 'Negocio',
            'direccion' => $registro['direccion'],
            'email' => strtolower(trim($registro['email'])),
            'password_hash' => Hash::make($registro['password']),
            'rol' => $rol,
        ];
        $locationColumn = $this->locationColumn();
        if ($locationColumn) {
            $user[$locationColumn] = 'Chapinero';
        }

        DB::table('usuario')->insert($user);

        return $idUsuario;
    }

    private function storeUpload(?UploadedFile $file, string $folder, string $prefix): string
    {
        abort_unless($file, 422, 'Falta un archivo requerido.');

        $directory = public_path('uploads/' . $folder);
        File::ensureDirectoryExists($directory);
        $filename = $prefix . '-' . Str::random(12) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/' . $folder . '/' . $filename;
    }

    public function registerUser(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'apellido' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terminos' => ['required', 'in:aceptado'],
        ]);

        DB::transaction(function () use ($validated): void {
            $nextId = ((int) DB::table('usuario')
                ->lockForUpdate()
                ->max('id_usuario')) + 1;

            $user = [
                'id_usuario' => $nextId,
                'nombre' => trim($validated['nombre']),
                'apellido' => trim($validated['apellido']),
                'email' => strtolower(trim($validated['email'])),
                'password_hash' => Hash::make($validated['password']),
                'rol' => 'usuario',
            ];
            $locationColumn = $this->locationColumn();
            if ($locationColumn) {
                $user[$locationColumn] = 'Chapinero';
            }

            DB::table('usuario')->insert($user);
        });

        return redirect()->route('registro.exitoso');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = DB::table('usuario')
            ->where('email', strtolower(trim($validated['email'])))
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password_hash)) {
            return back()->withErrors(['email' => 'El correo o la contraseña no son correctos.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        $role = strtolower(trim((string) $user->rol));
        if ($role === 'negocio') {
            $business = DB::table('negocios')->where('id_usuario', $user->id_usuario)->exists();
            if (!$business) {
                return back()->withErrors(['email' => 'No hay un negocio asociado a esta cuenta.'])
                    ->withInput($request->only('email'));
            }

            $request->session()->forget('auth_user');
            return redirect()->away(
                rtrim((string) config('services.shizen.business_url'), '/') . '/login'
            );
        }

        if ($role === 'repartidor') {
            $request->session()->forget('auth_user');
            return redirect()->away(
                rtrim((string) config('services.shizen.courier_url'), '/') . '/'
            );
        }

        if ($role !== 'usuario') {
            return back()->withErrors(['email' => 'Esta cuenta no tiene un rol válido.'])
                ->withInput($request->only('email'));
        }

        $localidad = $user->localidad ?? $user->ciudad ?? null;
        $request->session()->put('auth_user', [
            'id_usuario' => $user->id_usuario,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'email' => $user->email,
            'direccion' => $user->direccion,
            'localidad' => $localidad,
            'ciudad' => $localidad,
            'rol' => $role,
        ]);

        $redirect = $this->safeRedirect((string) $request->input('redirect', '/'));

        return redirect()->to($redirect);
    }

    public function profile(Request $request)
    {
        $authUser = $request->session()->get('auth_user');
        if (!is_array($authUser) || empty($authUser['id_usuario'])) {
            return redirect()->route('login', ['redirect' => '/perfil']);
        }

        $user = DB::table('usuario')->where('id_usuario', $authUser['id_usuario'])->first();
        if (!$user) {
            $request->session()->forget('auth_user');

            return redirect()->route('login')->withErrors([
                'email' => 'Tu sesión expiró. Inicia sesión nuevamente.',
            ]);
        }

        $avatarFiles = $this->profileAvatarPaths();
        $avatar = $authUser['avatar'] ?? ($avatarFiles[0] ?? '');

        return view('perfil', [
            'profileUser' => array_merge($authUser, [
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'email' => $user->email,
                'direccion' => $user->direccion,
                'localidad' => $user->localidad ?? $user->ciudad ?? null,
                'avatar' => $avatar,
            ]),
            'avatarFiles' => $avatarFiles,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $authUser = $request->session()->get('auth_user');
        abort_unless(is_array($authUser) && !empty($authUser['id_usuario']), 403);
        abort_unless(DB::table('usuario')->where('id_usuario', $authUser['id_usuario'])->exists(), 403);

        $avatarFiles = $this->profileAvatarPaths();
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'apellido' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ .\'-]+$/u'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'localidad' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'string', Rule::in($avatarFiles)],
        ]);

        $changes = [
            'nombre' => trim($validated['nombre']),
            'apellido' => trim($validated['apellido']),
            'direccion' => $validated['direccion'] ?? null,
        ];
        $locationColumn = $this->locationColumn();
        $localidad = $validated['localidad'] ?? $validated['ciudad'] ?? null;
        if ($locationColumn) {
            $changes[$locationColumn] = $localidad;
        }

        DB::table('usuario')->where('id_usuario', $authUser['id_usuario'])->update($changes);
        $updatedAuthUser = array_merge($authUser, $changes, [
            'localidad' => $localidad,
            'ciudad' => $localidad,
        ]);
        if (!empty($validated['avatar'])) {
            $updatedAuthUser['avatar'] = $validated['avatar'];
        }
        $request->session()->put('auth_user', $updatedAuthUser);

        return redirect()->route('profile')->with('profile_success', 'Tus datos fueron actualizados.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('auth_user');
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function locationColumn(): ?string
    {
        if (Schema::hasColumn('usuario', 'localidad')) {
            return 'localidad';
        }

        return Schema::hasColumn('usuario', 'ciudad') ? 'ciudad' : null;
    }

    private function profileAvatarPaths(): array
    {
        $directory = public_path('assets/Perfil');
        if (!File::isDirectory($directory)) {
            return [];
        }

        return array_map(
            static fn ($file): string => 'assets/Perfil/' . $file->getFilename(),
            array_filter(File::files($directory), static fn ($file): bool =>
                in_array(strtolower($file->getExtension()), ['png', 'jpg', 'jpeg', 'webp'], true)
            )
        );
    }

    private function safeRedirect(string $redirect): string
    {
        $path = parse_url($redirect, PHP_URL_PATH);
        $query = [];
        parse_str((string) parse_url($redirect, PHP_URL_QUERY), $query);

        if (is_string($path) && !Str::startsWith($path, '/')) {
            $legacy = ltrim($path, '/');
            if (in_array($legacy, ['index.php', 'php/index.php'], true)) {
                return '/';
            }
            if (in_array($legacy, ['php/pedidos.php', 'pedidos.php'], true)) {
                return '/pedidos';
            }
            if (in_array($legacy, ['php/favoritos.php', 'favoritos.php'], true)) {
                return '/favoritos';
            }
            if (in_array($legacy, ['php/notificaciones.php', 'notificaciones.php'], true)) {
                return '/notificaciones';
            }
            if (in_array($legacy, ['php/promociones.php', 'promociones.php'], true)) {
                return '/promociones';
            }
            if (in_array($legacy, ['php/categorias.php', 'categorias.php'], true)) {
                return '/categorias' . ($query ? '?' . http_build_query($query) : '');
            }
            if ($legacy === 'php/negocio.php' && filter_var($query['id'] ?? null, FILTER_VALIDATE_INT)) {
                return '/negocios/' . (int) $query['id'] . '/menu';
            }
            if ($legacy === 'php/pedido.php' && filter_var($query['id'] ?? null, FILTER_VALIDATE_INT)) {
                return '/pedidos/' . (int) $query['id'];
            }

            return '/';
        }

        if (
            !is_string($path) ||
            !Str::startsWith($redirect, '/') ||
            Str::startsWith($redirect, '//') ||
            str_contains($redirect, '\\') ||
            preg_match('/[\r\n]/', $redirect)
        ) {
            return '/';
        }

        return $redirect;
    }

}
