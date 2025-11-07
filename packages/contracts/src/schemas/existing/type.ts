import { z } from 'zod';
import { BaseEntitySchema } from '../common';

export const TypeSchema = BaseEntitySchema.extend({
  name: z.string().max(255),
  label_abbreviation: z.string().max(50).nullable().optional(),
  color: z.string().max(50).default('#808080'),
  description: z.string().nullable().optional(),
});

export const CreateTypeSchema = TypeSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateTypeSchema = CreateTypeSchema.partial();

export type Type = z.infer<typeof TypeSchema>;
export type CreateType = z.infer<typeof CreateTypeSchema>;
export type UpdateType = z.infer<typeof UpdateTypeSchema>;
