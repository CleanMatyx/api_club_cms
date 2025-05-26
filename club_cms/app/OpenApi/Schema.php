<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *  version="1.0.0",
 *  title="Club CMS API",
 *  description="Documentación de la API para el Club CMS",
 *  @OA\Contact(email="soporte@clubcms.com")
 * )
 *
 * @OA\Server(
 *  url="http://localhost/api",
 *  description="Base URL para todos los endpoints"
 * )
 * 
 * @OA\SecurityScheme(
 *  securityScheme="bearerAuth",
 *  type="http",
 *  scheme="bearer",
 *  bearerFormat="JWT"
 * )
 */
class Schema
{
    /**
     * @OA\Schema(
     *  schema="CourtRequest",
     *  type="object",
     *  required={"sport_id","name","location"},
     *  @OA\Property(property="sport_id", type="integer", example=1),
     *  @OA\Property(property="name", type="string", example="Pista 1"),
     *  @OA\Property(property="location", type="string", example="Localización de la pista")
     * )
     */
    private $courtRequest;

    /**
     * @OA\Schema(
     *  schema="CourtResource",
     *  type="object",
     *  @OA\Property(property="id", type="integer", example=1),
     *  @OA\Property(property="name", type="string", example="Pista 1"),
     *  @OA\Property(property="location", type="string", example="Localización de la pista")
     * )
     */
    private $courtResource;

    /**
     * @OA\Schema(
     *  schema="MemberRequest",
     *  type="object",
     *  required={"name","membership_date","status"},
     *  @OA\Property(property="name", type="string", example="Juan Pérez"),
     *  @OA\Property(property="email", type="string", format="email", example="juan@email.com"),
     *  @OA\Property(property="phone", type="string", example="+34 123 456 789"),
     *  @OA\Property(property="membership_date", type="string", format="date", example="2023-01-01"),
     *  @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended"}, example="active")
     * )
     */
    private $memberRequest;

    /**
     * @OA\Schema(
     *  schema="MemberResource",
     *  type="object",
     *  @OA\Property(property="id", type="integer", example=42),
     *  @OA\Property(property="name", type="string", example="Juan Pérez"),
     *  @OA\Property(property="email", type="string", format="email", example="juan@email.com"),
     *  @OA\Property(property="phone", type="string", example="+34 123 456 789"),
     *  @OA\Property(property="membership_date", type="string", format="date", example="2023-01-01"),
     *  @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended"}, example="active"),
     *  @OA\Property(property="has_system_access", type="boolean", example=false),
     *  @OA\Property(
     *    property="user", 
     *    type="object", 
     *    nullable=true,
     *    @OA\Property(property="id", type="integer", example=12),
     *    @OA\Property(property="name", type="string", example="Juan Pérez"),
     *    @OA\Property(property="role", type="string", enum={"user", "admin"}, example="user")
     *  )
     * )
     */
    private $memberResource;

    /**
     * @OA\Schema(
     *  schema="MemberCreateResponse",
     *  type="object",
     *  @OA\Property(property="ok", type="boolean", example=true),
     *  @OA\Property(property="message", type="string", example="Miembro creado correctamente"),
     *  @OA\Property(
     *    property="member",
     *    type="object",
     *    @OA\Property(property="id", type="integer", example=32),
     *    @OA\Property(property="name", type="string", example="Juan Pérez"),
     *    @OA\Property(property="membership_date", type="string", format="date", example="2023-01-01"),
     *    @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended"}, example="active")
     *  )
     * )
     */
    private $memberCreateResponse;

    /**
     * @OA\Schema(
     *  schema="ReservationRequest",
     *  type="object",
     *  required={"member_id","court_id","date","hour"},
     *  @OA\Property(property="member_id", type="integer", example=1),
     *  @OA\Property(property="court_id", type="integer", example=1),
     *  @OA\Property(property="date", type="string", format="date", example="2023-01-01"),
     *  @OA\Property(property="hour", type="string", format="time", example="14:00")
     * )
     */
    private $reservationRequest;

    /**
     * @OA\Schema(
     *  schema="ReservationResource",
     *  type="object",
     *  @OA\Property(property="id", type="integer", example=1),
     *  @OA\Property(property="member_id", type="integer", example=1),
     *  @OA\Property(property="court_name", type="string", example="Pista 1"),
     *  @OA\Property(property="date", type="string", format="date", example="2023-01-01"),
     *  @OA\Property(property="hour", type="string", format="time", example="14:00")
     * )
     */
    private $reservationResource;

    /**
     * @OA\Schema(
     *  schema="SportRequest",
     *  type="object",
     *  required={"name"},
     *  @OA\Property(property="name", type="string", example="Fútbol"),
     *  @OA\Property(property="description", type="string", example="Descripción del deporte")
     * )
     */
    private $sportRequest;

    /**
     * @OA\Schema(
     *  schema="SportResource",
     *  type="object",
     *  @OA\Property(property="id", type="integer", example=1),
     *  @OA\Property(property="name", type="string", example="Fútbol"),
     *  @OA\Property(property="description", type="string", example="Descripción del deporte")
     * )
     */
    private $sportResource;

    /**
     * @OA\Schema(
     *  schema="UserRequest",
     *  type="object",
     *  required={"name","email","password","password_confirmation"},
     *  @OA\Property(property="name", type="string", example="Juan Pérez"),
     *  @OA\Property(property="email", type="string", format="email", example="correo@correo.com"),
     *  @OA\Property(property="password", type="string", format="password", example="contraseña123"),
     *  @OA\Property(property="password_confirmation", type="string", format="password", example="contraseña123"),
     *  @OA\Property(property="role", type="string", enum={"user", "admin"}, example="user")
     * )
     */
    private $userRequest;

    /**
     * @OA\Schema(
     *  schema="UserResource",
     *  type="object",
     *  @OA\Property(property="id", type="integer", example=1),
     *  @OA\Property(property="name", type="string", example="Juan Pérez"),
     *  @OA\Property(property="email", type="string", format="email", example="correo@correo.com"),
     *  @OA\Property(property="role", type="string", enum={"user", "admin"}, example="user"),
     *  @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-25T10:30:00Z")
     * )
     */
    private $userResource;

    /**
     * @OA\Schema(
     *  schema="AuthRequest",
     *  type="object",
     *  required={"email","password"},
     *  @OA\Property(property="email", type="string", format="email", example="usuario@correo.com"),
     * @OA\Property(property="password", type="string", format="password", example="contraseña123")
     * )
     */
    private $authRequest;

    /**
     * @OA\Schema(
     *   schema="LoginResponse",
     *   type="object",
     *   @OA\Property(property="ok", type="boolean", example=true),
     *   @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJh..."),
     * )
     */
    private $loginResponse;

    /**
     * @OA\Schema(
     *   schema="LogoutResponse",
     *   type="object",
     *   @OA\Property(property="ok", type="boolean", example=true),
     *   @OA\Property(property="message", type="string", example="Sesión cerrada correctamente")
     * )
     */
    private $logoutResponse;

        /**
     * @OA\Schema(
     *   schema="LoginRequest",
     *   type="object",
     *   required={"email","password"},
     *   @OA\Property(property="email", type="string", format="email", example="admin@admin.com"),
     *   @OA\Property(property="password",type="string", format="password", example="admin")
     * )
     */
    private $loginRequest;

    /**
     * @OA\Schema(
     *   schema="ErrorResponse",
     *   type="object",
     *   description="Respuesta estándar para errores internos del servidor",
     *   @OA\Property(property="ok", type="boolean", example=false),
     *   @OA\Property(
     *     property="message",
     *     type="string", 
     *     example="Error interno del servidor al procesar la solicitud.",
     *     description="Mensaje de error seguro que no expone detalles técnicos"
     *   )
     * )
     */
    private $errorResponse;

    /**
     * @OA\Schema(
     *   schema="ValidationErrorResponse",
     *   type="object",
     *   description="Respuesta cuando falla la validación de los datos de la petición",
     *   @OA\Property(
     *     property="ok",
     *     type="boolean",
     *     example=false,
     *     description="Indica que la operación falló"
     *   ),
     *   @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Error de validación",
     *     description="Mensaje de error general"
     *   ),
     *   @OA\Property(
     *     property="errors",
     *     type="object",
     *     description="Listado de errores de validación por campo",
     *     example={
     *       "email": {"El correo electrónico ya está registrado por otro miembro."},
     *       "member_id": {"El miembro ya tiene el máximo de 3 reservas permitidas para esta fecha."},
     *       "court_id": {"Ya existe una reserva para esta cancha en la misma fecha y hora."},
     *       "date": {"La fecha no puede ser anterior a hoy."},
     *       "hour": {"El horario debe estar entre las 08:00 y las 21:00."},
     *       "name": {"El campo nombre es obligatorio."}
     *     }
     *   )
     * )
     */
    private $validationErrorResponse;

    /**
     * @OA\Schema(
     *  schema="MemberUpdateResponse",
     *  type="object",
     *  @OA\Property(property="ok", type="boolean", example=true),
     *  @OA\Property(property="message", type="string", example="Miembro actualizado correctamente"),
     *  @OA\Property(
     *    property="member",
     *    type="object",
     *    @OA\Property(property="id", type="integer", example=32),
     *    @OA\Property(property="name", type="string", example="Juan Pérez"),
     *    @OA\Property(property="membership_date", type="string", format="date", example="2023-01-01"),
     *    @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended"}, example="active")
     *  )
     * )
     */
    private $memberUpdateResponse;

    /**
     * @OA\Schema(
     *  schema="ReservationCreateResponse",
     *  type="object",
     *  @OA\Property(property="ok", type="boolean", example=true),
     *  @OA\Property(property="message", type="string", example="Reserva creada correctamente"),
     *  @OA\Property(
     *    property="reservation",
     *    type="object",
     *    @OA\Property(property="id", type="integer", example=25),
     *    @OA\Property(property="member_id", type="integer", example=1),
     *    @OA\Property(property="court_name", type="string", example="Pista 1"),
     *    @OA\Property(property="date", type="string", format="date", example="2023-01-01"),
     *    @OA\Property(property="hour", type="string", format="time", example="14:00")
     *  )
     * )
     */
    private $reservationCreateResponse;

    /**
     * @OA\Schema(
     *  schema="ReservationUpdateResponse",
     *  type="object",
     *  @OA\Property(property="ok", type="boolean", example=true),
     *  @OA\Property(property="message", type="string", example="Reserva actualizada correctamente"),
     *  @OA\Property(
     *    property="reservation",
     *    type="object",
     *    @OA\Property(property="id", type="integer", example=25),
     *    @OA\Property(property="member_id", type="integer", example=1),
     *    @OA\Property(property="court_name", type="string", example="Pista 1"),
     *    @OA\Property(property="date", type="string", format="date", example="2023-01-01"),
     *    @OA\Property(property="hour", type="string", format="time", example="14:00")
     *  )
     * )
     */
    private $reservationUpdateResponse;

}
