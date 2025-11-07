import { z } from 'zod';
import { BaseEntitySchema, DataTypeSchema } from '../common';

export const AttributeSchema = BaseEntitySchema.extend({
  name: z.string().max(255),
  label_abbreviation: z.string().max(50).nullable().optional(),
  data_type: DataTypeSchema,
  unit_of_measure: z.string().max(50).nullable().optional(),
  description: z.string().nullable().optional(),
});

export const CreateAttributeSchema = AttributeSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateAttributeSchema = CreateAttributeSchema.partial();

export type Attribute = z.infer<typeof AttributeSchema>;
export type CreateAttribute = z.infer<typeof CreateAttributeSchema>;
export type UpdateAttribute = z.infer<typeof UpdateAttributeSchema>;
