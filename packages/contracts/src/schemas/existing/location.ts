import { z } from 'zod';
import { BaseEntitySchema, UuidSchema } from '../common';

export const LocationSchema = BaseEntitySchema.extend({
  assessment_id: UuidSchema,
  parent_location_id: UuidSchema.nullable().optional(),
  name: z.string().max(255),
  label_abbreviation: z.string().max(50).nullable().optional(),
  description: z.string().nullable().optional(),
  sort_order: z.number().int().default(0),
});

export const CreateLocationSchema = LocationSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateLocationSchema = CreateLocationSchema.partial();

export type Location = z.infer<typeof LocationSchema>;
export type CreateLocation = z.infer<typeof CreateLocationSchema>;
export type UpdateLocation = z.infer<typeof UpdateLocationSchema>;
