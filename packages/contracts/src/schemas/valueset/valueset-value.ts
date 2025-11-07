import { z } from 'zod';
import { BaseEntitySchema, UuidSchema } from '../common';

export const ValuesetValueSchema = BaseEntitySchema.extend({
  valueset_id: UuidSchema,
  value: z.string().max(255),
  display_label: z.string().max(255).nullable().optional(),
  sort_order: z.number().int().default(0),
  is_active: z.boolean().default(true),
});

export const CreateValuesetValueSchema = ValuesetValueSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateValuesetValueSchema = CreateValuesetValueSchema.partial();

export type ValuesetValue = z.infer<typeof ValuesetValueSchema>;
export type CreateValuesetValue = z.infer<typeof CreateValuesetValueSchema>;
export type UpdateValuesetValue = z.infer<typeof UpdateValuesetValueSchema>;
