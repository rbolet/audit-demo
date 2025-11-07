import { z } from 'zod';
import { BaseEntitySchema, UuidSchema } from '../common';

export const ExistingAttributeValueSchema = BaseEntitySchema.extend({
  existing_id: UuidSchema,
  attribute_id: UuidSchema,
  value: z.string(),
});

export const CreateExistingAttributeValueSchema = ExistingAttributeValueSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateExistingAttributeValueSchema = CreateExistingAttributeValueSchema.partial();

export type ExistingAttributeValue = z.infer<typeof ExistingAttributeValueSchema>;
export type CreateExistingAttributeValue = z.infer<typeof CreateExistingAttributeValueSchema>;
export type UpdateExistingAttributeValue = z.infer<typeof UpdateExistingAttributeValueSchema>;
