import { z } from 'zod';
import { BaseEntitySchema } from '../common';

export const SiteSchema = BaseEntitySchema.extend({
  site_name: z.string().max(255),
  site_address: z.string().max(255),
  site_address_2: z.string().max(255).nullable().optional(),
  site_city: z.string().max(100),
  site_state: z.string().max(50),
  site_postal_code: z.string().max(20),
  site_contact_name: z.string().max(255).nullable().optional(),
  site_contact_phone: z.string().max(50).nullable().optional(),
  site_contact_email: z.string().email().max(255).nullable().optional(),
});

export const CreateSiteSchema = SiteSchema.omit({
  id: true,
  created_at: true,
  updated_at: true,
  deleted_at: true,
});

export const UpdateSiteSchema = CreateSiteSchema.partial();

export type Site = z.infer<typeof SiteSchema>;
export type CreateSite = z.infer<typeof CreateSiteSchema>;
export type UpdateSite = z.infer<typeof UpdateSiteSchema>;
