import { z } from 'zod';

/**
 * Example schema demonstrating the pattern
 * Replace with actual audit-related schemas as needed
 */
export const exampleSchema = z.object({
  id: z.number().int().positive(),
  name: z.string().min(1).max(255),
  description: z.string().optional(),
  createdAt: z.string().datetime(),
  updatedAt: z.string().datetime(),
});

export type Example = z.infer<typeof exampleSchema>;

/**
 * Schema for creating a new example (omit auto-generated fields)
 */
export const createExampleSchema = exampleSchema.omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export type CreateExample = z.infer<typeof createExampleSchema>;

/**
 * Schema for updating an example (all fields optional except id)
 */
export const updateExampleSchema = exampleSchema.partial().required({ id: true });

export type UpdateExample = z.infer<typeof updateExampleSchema>;
