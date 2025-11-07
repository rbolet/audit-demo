import { z } from 'zod';

// Common patterns and base schemas
export const UuidSchema = z.string().uuid();

export const TimestampsSchema = z.object({
  created_at: z.coerce.date(),
  updated_at: z.coerce.date(),
  deleted_at: z.coerce.date().nullable().optional(),
});

// Assessment Status Enum
export const AssessmentStatusSchema = z.enum([
  'PLANNED',
  'ASSIGNED',
  'IN_PROGRESS',
  'IN_QC',
  'COMPLETE',
]);

export type AssessmentStatus = z.infer<typeof AssessmentStatusSchema>;

// Data Type Enum (for attributes and valuesets)
export const DataTypeSchema = z.enum(['NUMBER', 'CHARACTERS']);
export type DataType = z.infer<typeof DataTypeSchema>;

// Base entity with UUID and timestamps
export const BaseEntitySchema = z
  .object({
    id: UuidSchema,
  })
  .merge(TimestampsSchema);

export type BaseEntity = z.infer<typeof BaseEntitySchema>;
