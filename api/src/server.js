import express from 'express';
import cors from 'cors';
import 'dotenv/config';
import { pool } from './db.js';

const app = express();

const allowOrigin = process.env.CORS_ORIGIN || '*';
app.use(cors({ origin: allowOrigin }));
app.use(express.json());

app.get('/health', (_req, res) => res.json({ ok: true }));

// GET /api/empanadas
app.get('/api/empanadas', async (_req, res) => {
  const [rows] = await pool.query('SELECT * FROM empanadas ORDER BY id DESC');
  res.json(rows);
});

// POST /api/empanada
app.post('/api/empanada', async (req, res) => {
  const { name, type, filling = null, price = null, is_sold_out = false } = req.body || {};
  if (!name || !type) return res.status(400).json({ error: 'name y type son obligatorios' });

  const [result] = await pool.execute(
    'INSERT INTO empanadas (name, type, filling, price, is_sold_out) VALUES (?, ?, ?, ?, ?)',
    [name, type, filling, price, !!is_sold_out]
  );
  const [created] = await pool.query('SELECT * FROM empanadas WHERE id = ?', [result.insertId]);
  res.status(201).json(created[0]);
});

// PUT /api/empanada/:id
app.put('/api/empanada/:id', async (req, res) => {
  const { id } = req.params;
  const { name, type, filling, price, is_sold_out } = req.body || {};
  const [result] = await pool.execute(
    `UPDATE empanadas SET
      name = COALESCE(?, name),
      type = COALESCE(?, type),
      filling = COALESCE(?, filling),
      price = COALESCE(?, price),
      is_sold_out = COALESCE(?, is_sold_out)
     WHERE id = ?`,
    [name, type, filling, price, is_sold_out, id]
  );
  if (!result.affectedRows) return res.status(404).json({ error: 'No encontrada' });
  const [row] = await pool.query('SELECT * FROM empanadas WHERE id = ?', [id]);
  res.json(row[0]);
});

// DELETE /api/empanada/:id
app.delete('/api/empanada/:id', async (req, res) => {
  const { id } = req.params;
  const [result] = await pool.execute('DELETE FROM empanadas WHERE id = ?', [id]);
  if (!result.affectedRows) return res.status(404).json({ error: 'No encontrada' });
  res.status(204).end();
});

const port = process.env.PORT || 3000;
app.listen(port, () => console.log(`API listening on :${port}`));
