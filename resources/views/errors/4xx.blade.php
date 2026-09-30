@extends('errors.minimo')

@section('codigo'){{ $exception->getStatusCode() }}@endsection
@section('titulo', 'No se pudo completar la solicitud')
@section('mensaje', 'Revisa la información e intenta de nuevo en unos momentos.')
