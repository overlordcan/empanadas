// api/__tests__/api.test.js
import { jest } from '@jest/globals';
import request from 'supertest';

// 1) Prepara un pool falso de oryeba
const mockPool = {
  query: jest.fn(async (sql, params) => {
    if (/ORDER BY id DESC/.test(sql)) return [[{ id: 1, name: 'Pino', type: 'Horno' }]];
    if (/WHERE id = \?/.test(sql))   return [[{ id: params[0], name: 'Pino', type: 'Horno' }]];
    return [[]];
  }),
  execute: jest.fn(async (sql, params) => {
    if (/INSERT INTO/.test(sql)) return [{ insertId: 99, affectedRows: 1 }];
    if (/UPDATE/.test(sql))     return [{ affectedRows: params.at(-1) === 1 ? 1 : 0 }];
    if (/DELETE/.test(sql))     return [{ affectedRows: params[0] === 1 ? 1 : 0 }];
    return [{ affectedRows: 0 }];
  })
};

// 2) Mockea
jest.unstable_mockModule('../src/db.js', () => ({
  __esModule: true,
  pool: mockPool
}));

// 3) Importa
const { default: app } = await import('../src/app.js');

test('GET lista empanadas', async () => {
  const res = await request(app).get('/api/empanadas');
  expect(res.status).toBe(200);
  expect(Array.isArray(res.body)).toBe(true);
});

test('POST valida campos obligatorios', async () => {
  const res = await request(app).post('/api/empanada').send({ type: 'Horno' });
  expect(res.status).toBe(400);
});

test('DELETE 404 cuando no existe', async () => {
  const res = await request(app).delete('/api/empanada/999');
  expect(res.status).toBe(404);
});
