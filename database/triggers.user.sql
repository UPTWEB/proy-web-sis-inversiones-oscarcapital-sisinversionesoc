CREATE OR REPLACE FUNCTION calcular_duracion_sesion()
RETURNS TRIGGER AS $$
BEGIN
    NEW.duracion := NEW.fin - NEW.inicio;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_calcular_duracion
BEFORE UPDATE ON sesiones
FOR EACH ROW
WHEN (NEW.fin IS NOT NULL)
EXECUTE FUNCTION calcular_duracion_sesion();