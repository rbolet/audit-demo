import { z } from 'zod';
import { AssessmentStatusSchema, BaseEntitySchema, UuidSchema } from '../common';

export const AssessmentSchema = BaseEntitySchema.extend({
  site_id: UuidSchema,
  root_location_id: UuidSchema.nullable().optional(),
  scheduled_date: z.coerce.date(),
  assigned_to_id: UuidSchema.nullable().optional(),
  assigned_date: z.coerce.date().nullable().optional(),
  status: AssessmentStatusSchema.default('PLANNED'),
});

export const CreateAssessmentSchema = AssessmentSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
  assigned_date: true,
});

export const UpdateAssessmentSchema = CreateAssessmentSchema.partial();

export type Assessment = z.infer<typeof AssessmentSchema>;
export type CreateAssessment = z.infer<typeof CreateAssessmentSchema>;
export type UpdateAssessment = z.infer<typeof UpdateAssessmentSchema>;
