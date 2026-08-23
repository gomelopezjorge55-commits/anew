require('dotenv').config();
const { Pool } = require('pg');

const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
  ssl: { rejectUnauthorized: false }
});

async function main() {
  try {
    console.log("Conectando a Neon Serverless...");
    await pool.query('ALTER TABLE clientes ADD COLUMN clave VARCHAR(10);');
    console.log("✅ ¡Éxito! Columna 'clave' ha sido añadida a la tabla 'clientes'.");
  } catch (error) {
    if (error.code === '42701') {
      console.log("⚠️ La columna 'clave' ya existía en la base de datos.");
    } else {
      console.error("❌ Ocurrió un error:", error.message);
    }
  } finally {
    await pool.end();
  }
}

main();
