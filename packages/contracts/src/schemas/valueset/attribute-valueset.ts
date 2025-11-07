import type { z } from 'zod';
import { BaseEntitySchema, UuidSchema } from '../common';

export const AttributeValuesetSchema = BaseEntitySchema.extend({
  attribute_id: UuidSchema,
  valueset_id: UuidSchema,
});

export const CreateAttributeValuesetSchema = AttributeValuesetSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateAttributeValuesetSchema = CreateAttributeValuesetSchema.partial();

export type AttributeValueset = z.infer<typeof AttributeValuesetSchema>;
export type CreateAttributeValueset = z.infer<typeof CreateAttributeValuesetSchema>;
export type UpdateAttributeValueset = z.infer<typeof UpdateAttributeValuesetSchema>;
