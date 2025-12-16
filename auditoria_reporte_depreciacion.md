# Auditoría del cálculo de depreciación (Reporte Depreciación)

## Ubicación exacta del cálculo
El cálculo del reporte se encuentra en `_Ajax.server.php`, dentro del bucle que genera el detalle mensual (`for ($m = $mes; $m <= $mes_fin; $m++)`). El SQL y la derivación de columnas están entre las líneas 680–759.

### SQL que alimenta Dep. Anterior / Gasto Dep. / Dep. Acum.
```sql
(select c.cdep_dep_acum
 from saecdep c
 where c.cdep_cod_acti = saecdep.cdep_cod_acti
   and c.act_cod_empr = saecdep.act_cod_empr
   and c.act_cod_sucu = saecdep.act_cod_sucu
   and c.cdep_ani_depr = $anio             -- año del reporte
   and c.cdep_mes_depr = $m)               -- MISMO mes del reporte
  as cdep_dep_acum,
sum(saecdep.cdep_gas_depn) as cdep_gas_depn,
```
Filtros clave del FROM/WHERE:
```sql
( saecdep.cdep_ani_depr between $anio and $anio_fin ) and
( saecdep.cdep_mes_depr = $m )
```
Luego se calculan en PHP:
```php
$deprAnterior  = $oIfx->f('cdep_dep_acum');
$gastoDepr     = $oIfx->f('cdep_gas_depn');
$deprAcumulada = $deprAnterior + $gastoDepr;  // Dep. Acum.
$valorPorDepr  = $valorCompra - $deprAcumulada;
```
【F:_Ajax.server.php†L680-L759】

## Por qué el valor es incorrecto
1. **Off-by-one de mes:** `cdep_dep_acum` se toma con `cdep_mes_depr = $m`, es decir, **el mismo mes del reporte**, no el mes anterior. Para un reporte de diciembre 2025 (`$m=12`, `$anio=2025`), `Dep. Anterior` trae el acumulado de diciembre, no de noviembre.
2. **Histórico recortado:** el WHERE limita `saecdep.cdep_ani_depr between $anio and $anio_fin`, por lo que **solo considera depreciaciones desde el año inicial del filtro**. Si el activo empezó en 2010 y el reporte se consulta desde 2025, toda la depreciación 2010–2024 queda excluida del cálculo de `cdep_dep_acum` y del SUM de `cdep_gas_depn`.
3. **Doble conteo en Dep. Acum.:** `Dep. Acum.` se arma como `Dep. Anterior + Gasto Dep.`. Cuando `cdep_dep_acum` ya incluye el gasto del mismo mes (habitual en acumulados), el código suma nuevamente el gasto, generando un monto inflado para el mes del reporte.
【F:_Ajax.server.php†L680-L759】

## Columnas afectadas
- **Dep. Anterior:** muestra el acumulado del mismo mes (y solo desde el año filtrado) en vez del mes anterior desde el inicio del activo.
- **Dep. Acum.:** arranca de un `Dep. Anterior` recortado y además suma de nuevo el gasto del mes → doble conteo y falta de histórico.
- **Valor por Depr.:** depende de `Dep. Acum.`, por lo que también queda subvalorado o sobredimensionado.
- Totales por grupo y totales generales replican los valores ya incorrectos.

## Reproducción (ejemplo diciembre 2025)
- Parámetros: `anio=2025`, `anio_fin=2025`, `mes=12`, `mes_fin=12`.
- El bucle fija `$m=12` y ejecuta el SQL con:
  - `cdep_mes_depr = 12`
  - `cdep_ani_depr between 2025 and 2025`
- Resultado: `Dep. Anterior` trae `cdep_dep_acum` de diciembre 2025. `Gasto Dep.` es el `cdep_gas_depn` de diciembre 2025 (sumado en el GROUP BY). `Dep. Acum.` = `cdep_dep_acum (dic-2025)` + `cdep_gas_depn (dic-2025)`.
- Esperado: `Dep. Anterior` debería acumular desde el inicio del activo hasta noviembre 2025; el código lo limita a diciembre 2025 y al año 2025.

## Recomendación de corrección
- **Mes anterior:** calcular `Dep. Anterior` con `cdep_mes_depr = $m-1` y ajustar año cuando `$m=1` (pasar a diciembre del año previo), o usar un filtro de fecha `< primer día del mes del reporte`.
- **Histórico completo:** eliminar el límite `between $anio and $anio_fin` para el acumulado previo y sumar desde la fecha de inicio del activo (`act_fdep_act` o equivalente) hasta el mes anterior.
- **Dep. Acum.:** obtenerlo directamente del acumulado al cierre del mes del reporte, o si se mantiene la suma, asegurar que `Dep. Anterior` represente solo hasta el mes previo para evitar doble conteo.
- Corrigiendo `Dep. Anterior` conforme a lo anterior, se alinearán automáticamente `Dep. Acum.` y `Valor por Depr.` (y los totales) porque dependen de ese acumulado.
