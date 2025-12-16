# Investigación cálculo de Depreciación (Reporte Depreciación)

## Tarea 1: ubicación del cálculo
- El listado detallado genera las columnas desde un `SELECT` en `_Ajax.server.php` (líneas 692-734). El cálculo lee `cdep_dep_acum` mediante un subquery:
  - `c.cdep_dep_acum` se busca para el mismo activo, empresa y sucursal, **filtrado por el año del reporte `$anio` y el mes iterado `$m`** (`c.cdep_ani_depr = $anio` y `c.cdep_mes_depr = $m`).【F:_Ajax.server.php†L692-L734】
  - El mismo `SELECT` filtra los registros de depreciación a `saecdep.cdep_ani_depr between $anio and $anio_fin` y `saecdep.cdep_mes_depr = $m` (solo el mes iterado). También exige que la fecha fin de vida (`act_fiman_act`) sea mayor al periodo de corte y que la compra sea anterior o igual al periodo.【F:_Ajax.server.php†L720-L734】
- El procesamiento en PHP toma esos campos y define:
  - `Dep_Anterior = $oIfx->f('cdep_dep_acum');`
  - `Gasto_Depr = $oIfx->f('cdep_gas_depn');`
  - `Dep_Acum = $deprAnterior + $gastoDepr;`
  - `Valor por Depr = ValorCompra - Dep_Acum`.
  Estas asignaciones están en las líneas 743-759.【F:_Ajax.server.php†L743-L759】

## Tarea 2: definición contable esperada vs. actual
- Esperado: `Dep_Anterior` = acumulado hasta el mes anterior, `Gasto_Depr` = depreciación del mes actual, `Dep_Acum` = suma de ambos.
- Actual: el subquery trae `cdep_dep_acum` **del mismo mes del reporte** (`cdep_mes_depr = $m`) y del mismo año (`cdep_ani_depr = $anio`). Luego el código vuelve a sumar `Gasto_Depr` para calcular `Dep_Acum`. Si `cdep_dep_acum` ya incluye la depreciación del mes actual (lo habitual en acumulados), `Dep_Anterior` muestra el acumulado hasta el mes actual y `Dep_Acum` duplica el mes. Si el acumulado fuera hasta el mes actual igualmente falta la separación mes anterior/mes actual.

## Tarea 3: revisión de hipótesis
- **Hipótesis A/B (acumular sólo dentro del rango del reporte / usar AñoDesde):** El `WHERE saecdep.cdep_ani_depr between $anio and $anio_fin` limita los registros a los años del filtro, por lo que con AñoHasta=2025 y AñoDesde=2025 **se ignora toda depreciación histórica previa a 2025**.【F:_Ajax.server.php†L720-L734】
- **Hipótesis C/E (mes mal alineado / off-by-one):** El subquery usa `cdep_mes_depr = $m` (mes del reporte) en lugar de "mes anterior", por lo que `Dep_Anterior` corresponde al mismo periodo mostrado, no al inmediatamente anterior.【F:_Ajax.server.php†L692-L734】
- **Hipótesis D (reinicio por vida útil / fechas):** Además del corte por año/mes, se filtra que `act_fiman_act` sea posterior al periodo (`(COALESCE(DATE_PART('year', act_fiman_act ),3000)*100+... ) > ($anio_fin*100 + $m)`) y que la compra sea previa o igual al periodo (`act_fcmp_act` condición). Estos filtros impiden depreciar más allá de la fecha fin o antes de la compra, pero no restablecen acumulados previos; el principal recorte es el rango de años/meses.【F:_Ajax.server.php†L720-L734】

## Tarea 4: trazabilidad por activo (ejemplo con AñoHasta=2025, MesHasta=Diciembre)
- El bucle mensual ejecuta el `SELECT` con `$m` = 12 y `$anio` = 2025. Las filas de `saecdep` consideradas cumplen:
  - `cdep_ani_depr between 2025 and 2025`
  - `cdep_mes_depr = 12`
  - `cdep_dep_acum` se lee también con `cdep_ani_depr = 2025` y `cdep_mes_depr = 12`.
- Por tanto, para cualquier activo con vida útil vigente en diciembre 2025, `Dep_Anterior` proviene del registro de diciembre 2025 (no noviembre) y `Dep_Acum` se calcula como `cdep_dep_acum (dic-2025) + cdep_gas_depn (dic-2025)`. El sistema está sumando únicamente el rango diciembre 2025 y no está acumulando desde el inicio del activo ni deteniendo el acumulado en noviembre, lo que explica la discrepancia contra contabilidad.
- Para verificar un activo específico, basta revisar en `saecdep` el registro del activo para 2025/12: el código sumará ese `cdep_dep_acum` y `cdep_gas_depn`, ignorando meses previos (ej.: sumando de `2025-12-01` a `2025-12-31` únicamente).【F:_Ajax.server.php†L692-L734】【F:_Ajax.server.php†L743-L759】

## Conclusión
- La columna "Dep. Anterior" en el reporte corresponde al acumulado **del mismo mes filtrado**, debido al `cdep_mes_depr = $m` tanto en el subquery como en el filtro del `SELECT`, y al recorte de años `between $anio and $anio_fin`. Esto provoca que para diciembre 2025 se use el acumulado de diciembre en lugar del acumulado hasta noviembre, y adicionalmente se excluye depreciación previa a 2025 cuando el filtro inicia en ese año.
