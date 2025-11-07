import { z } from 'zod';
import { BaseEntitySchema, UuidSchema } from '../common';

export const TypeAttributeSchema = BaseEntitySchema.extend({
  type_id: UuidSchema,
  attribute_id: UuidSchema,
  label_concat_order: z.number().int().nullable().optional(),
  is_required: z.boolean().default(false),
});

export const CreateTypeAttributeSchema = TypeAttributeSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateTypeAttributeSchema = CreateTypeAttributeSchema.partial();

export type TypeAttribute = z.infer<typeof TypeAttributeSchema>;
export type CreateTypeAttribute = z.infer<typeof CreateTypeAttributeSchema>;
export type UpdateTypeAttribute = z.infer<typeof UpdateTypeAttributeSchema>;
