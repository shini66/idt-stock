# Admin bootstrap
Safely establish the first administrator during registration.
Preserve the operator default after an administrator exists.

## Specs
S1. “tenemos un problema cuando se registra siempre el usuario que como operador y nunda hay un admin como podemos resolver esto”
S2. “Primer registro como admin”
“tome siempre el primero para esta ocacion”

## Tasks
T1 | S1-S2 | Assign the existing earliest user admin, then bootstrap the first new user with concurrency-safe authorization | completed
T2 | S1-S2 | Add security-focused registration tests and verify the role boundary | completed

## Log
L1. “tenemos un problema cuando se registra siempre el usuario que como operador y nunda hay un admin como podemos resolver esto”
L2. “Primer registro como admin”
L3. “tome siempre el primero para esta ocacion”
