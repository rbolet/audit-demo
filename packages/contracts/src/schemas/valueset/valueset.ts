import { z } from 'zod';
import { BaseEntitySchema, DataTypeSchema } from '../common';

export const ValuesetSchema = BaseEntitySchema.extend({
  name: z.string().max(255),
  label_abbreviation: z.string().max(50).nullable().optional(),
  data_type: DataTypeSchema,
  description: z.string().nullable().optional(),
});

export const CreateValuesetSchema = ValuesetSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateValuesetSchema = CreateValuesetSchema.partial();

export type Valueset = z.infer<typeof ValuesetSchema>;
export type CreateValueset = z.infer<typeof CreateValuesetSchema>;
export type UpdateValueset = z.infer<typeof UpdateValuesetSchema>;
