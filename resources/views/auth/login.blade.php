<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Formatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .system-title {
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="login-card p-5">
                        <div class="text-center mb-4">
                            <h2 class="system-title mb-2">Sistema de Formatos</h2>
                        
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p class="mb-0">❌ {{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info">
                                ℹ️ {{ session('info') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username" 
                                       value="{{ old('username') }}" required autofocus placeholder="Ingresa tu usuario">
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" 
                                       required placeholder="Ingresa tu contraseña">
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                                Ingresar al Sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>