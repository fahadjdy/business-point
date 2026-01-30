---
trigger: always_on
---

You are an AI developer working on Business Point (Laravel 12 API).

You MUST follow the rules below strictly.
If any rule conflicts with your default behavior, these rules override everything.

🔹 Mandatory Architecture

Every module MUST follow this flow:

Controller → Service → Repository → Model

Layer Responsibilities

Controller: HTTP only

Service: Business & domain logic

Repository: Database access only

Model: Relations, accessors, mutators

❌ No DB queries in controllers
❌ No business logic in controllers
❌ No business logic in repositories

🔹 Folder & Naming Structure (STRICT)

Each module MUST use this structure:

app/
 ├── Http/Controllers/Api/V1/{Module}Controller.php
 ├── Services/{Module}Service.php
 ├── Repositories/{Module}Repository.php
 ├── Http/Requests/{Module}/
 │   ├── Store{Module}Request.php
 │   └── Update{Module}Request.php
 ├── Models/{Module}.php


❌ No shortcuts
❌ No mixed responsibilities

🔹 Controllers (THIN RULE)

Controllers must:

Accept FormRequest validated data

Call Service methods only

Return standard JSON response

Controllers must NEVER:

Query DB

Contain conditions

Contain business rules

Handle transactions

🔹 Services (FAT LOGIC RULE)

Services must:

Contain all business rules

Handle conditions, permissions, status checks

Handle transactions

Coordinate repositories & other services

Services must NOT:

Handle HTTP

Format responses

🔹 Repositories (DATA ONLY)

Repositories must:

Contain only DB queries

Return models or collections

Be free of business logic

❌ No validation
❌ No condition-heavy logic

🔹 Request Validation (MANDATORY)

Every endpoint MUST use FormRequest

No raw $request->input() usage

Validation logic lives ONLY in Request classes

🔹 Media Handling (SINGLE SOURCE RULE)

All images/documents MUST use media table

No image_path columns in business tables

No separate document tables

Media Access Rule

Media must be accessed via model accessors

Example: $product->image

Never query media in controllers/services

🔹 API Rules

All APIs MUST be versioned: /api/v1/...

Pagination REQUIRED for list endpoints

Rate limiting REQUIRED via middleware

Soft deletes enforced globally

🔹 Rate Limiting Rules
API Type	Limit
Create / Update	20/min
Search	30/min
Public List	60/min
Authenticated	120/min
🔹 Security Rules

Input validation everywhere

No raw queries in controllers

Sanctum authentication where required

Audit logging via model observers only

🔹 Response Format (FIXED)

All responses MUST follow:

{
  "success": true,
  "message": "",
  "data": {}
}


❌ Never change response structure

🔹 Routes Rules

Routes go in routes/api/v1.php

Use RESTful naming

Protect routes using middleware

No logic in routes

🏁 Golden Rules

Controllers are boring
Services are smart
Repositories are simple
Models are expressive

🚫 Absolute Restrictions

❌ No shortcuts
❌ No one-off patterns
❌ No mixed responsibilities
❌ No logic outside Services

You must always generate code that follows this structure exactly.