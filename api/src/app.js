import express from 'express';
import cors from 'cors';
import 'dotenv/config';
import * as db from './db.js';

const app = express();
app.use(cors({ origin: process.env.CORS_ORIGIN || '*' }));
app.use(express.json());

// helper: capturar errores async
const ah = (fn) => (req, res, next) => Promise.resolve(fn(req, res, next)).catch(next);
// normaliza undefined -> null para mysql2
const nn = (v) => (v === undefined ? null : v);

app.get('/health', (_req, res) => res.json({ ok: true }));

app.get('/api/empanadas', ah(async (_req, res) => {
  const [rows] = await db.pool.query('SELECT * FROM empanadas ORDER BY id DESC');
  res.json(rows);
}));

app.post('/api/empanada', ah(async (req, res) => {
  const { name, type, filling, price, is_sold_out } = req.body || {};
  if (!name || !type) return res.status(400).json({ error: 'name y type son obligatorios' });

  const params = [name, type, filling, price, is_sold_out ? 1 : 0].map(nn);
  const [r] = await db.pool.execute(
    'INSERT INTO empanadas (name, type, filling, price, is_sold_out) VALUES (?, ?, ?, ?, ?)',
    params
  );
  const [created] = await db.pool.query('SELECT * FROM empanadas WHERE id = ?', [r.insertId]);
  res.status(201).json(created[0]);
}));

app.put('/api/empanada/:id', ah(async (req, res) => {
  const id = Number(req.params.id);
  if (!Number.isInteger(id) || id <= 0) return res.status(400).json({ error: 'id inválido' });

  const { name, type, filling, price, is_sold_out } = req.body || {};
  const sold = (is_sold_out === undefined) ? null : (is_sold_out ? 1 : 0);
  const params = [name, type, filling, price, sold, id].map(nn);

  const [r] = await db.pool.execute(
    `UPDATE empanadas SET
      name        = COALESCE(?, name),
      type        = COALESCE(?, type),
      filling     = COALESCE(?, filling),
      price       = COALESCE(?, price),
      is_sold_out = COALESCE(CAST(? AS SIGNED), is_sold_out)
     WHERE id = ?`,
    params
  );
  if (!r.affectedRows) return res.status(404).json({ error: 'No encontrada' });
  const [row] = await db.pool.query('SELECT * FROM empanadas WHERE id = ?', [id]);
  res.json(row[0]);
}));

app.delete('/api/empanada/:id', ah(async (req, res) => {
  const id = Number(req.params.id);
  if (!Number.isInteger(id) || id <= 0) return res.status(400).json({ error: 'id inválido' });

  const [r] = await db.pool.execute('DELETE FROM empanadas WHERE id = ?', [id]);
  if (!r.affectedRows) return res.status(404).json({ error: 'No encontrada' });
  res.status(204).end();
}));

app.use((err, _req, res, _next) => {
  console.error('[API ERROR]', err);
  res.status(500).json({ error: 'internal_error' });
});

export default app;
