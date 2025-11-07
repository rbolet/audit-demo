import { z } from 'zod';
import { BaseEntitySchema, UuidSchema } from '../common';

export const ExistingSchema = BaseEntitySchema.extend({
  type_id: UuidSchema,
  location_id: UuidSchema,
  name: z.string().max(255).nullable().optional(),
  label: z.string().max(255),
  label_abbr: z.string().max(100).nullable().optional(),
  attribute_values_hash: z.string().max(255).nullable().optional(),
  quantity: z.number().int().min(1).default(1),
  notes: z.string().nullable().optional(),
});

export const CreateExistingSchema = ExistingSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
  label: true,
  attribute_values_hash: true,
});

export const UpdateExistingSchema = CreateExistingSchema.partial();

export type Existing = z.infer<typeof ExistingSchema>;
export type CreateExisting = z.infer<typeof CreateExistingSchema>;
export type UpdateExisting = z.infer<typeof UpdateExistingSchema>;
