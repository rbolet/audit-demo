import { describe, it, expect } from 'vitest';
import { ZodError } from 'zod';
import {
  SiteSchema,
  CreateSiteSchema,
  UpdateSiteSchema,
  type Site,
  type CreateSite,
  type UpdateSite,
} from '../src/schemas/assessment/site.js';

describe('Site Schemas', () => {
  describe('SiteSchema', () => {
    const validSiteBase = {
      id: '550e8400-e29b-41d4-a716-446655440000',
      created_at: new Date('2023-01-15T10:30:00Z'),
      updated_at: new Date('2023-01-15T10:30:00Z'),
      site_name: 'Main Office Building',
      site_address: '123 Business Street',
      site_city: 'Business City',
      site_state: 'BC',
      site_postal_code: '12345',
    };

    it('validates a complete valid site', () => {
      const validSite: Site = {
        ...validSiteBase,
        site_address_2: 'Suite 100',
        site_contact_name: 'John Doe',
        site_contact_phone: '555-123-4567',
        site_contact_email: 'john.doe@company.com',
        deleted_at: null,
      };

      expect(() => SiteSchema.parse(validSite)).not.toThrow();
      const result = SiteSchema.parse(validSite);
      expect(result.site_name).toBe(validSite.site_name);
      expect(result.site_contact_email).toBe(validSite.site_contact_email);
    });

    it('validates site with minimal required fields', () => {
      expect(() => SiteSchema.parse(validSiteBase)).not.toThrow();
      const result = SiteSchema.parse(validSiteBase);
      expect(result.site_name).toBe(validSiteBase.site_name);
      expect(result.site_contact_name).toBeUndefined();
    });

    it('validates site with null optional fields', () => {
      const siteWithNulls = {
        ...validSiteBase,
        site_address_2: null,
        site_contact_name: null,
        site_contact_phone: null,
        site_contact_email: null,
      };

      expect(() => SiteSchema.parse(siteWithNulls)).not.toThrow();
      const result = SiteSchema.parse(siteWithNulls);
      expect(result.site_address_2).toBeNull();
      expect(result.site_contact_name).toBeNull();
    });

    it('rejects site with missing required fields', () => {
      const missingSiteName = { ...validSiteBase };
      delete (missingSiteName as Partial<typeof missingSiteName>).site_name;

      const missingSiteAddress = { ...validSiteBase };
      delete (missingSiteAddress as Partial<typeof missingSiteAddress>).site_address;

      const missingSiteCity = { ...validSiteBase };
      delete (missingSiteCity as Partial<typeof missingSiteCity>).site_city;

      expect(() => SiteSchema.parse(missingSiteName)).toThrow(ZodError);
      expect(() => SiteSchema.parse(missingSiteAddress)).toThrow(ZodError);
      expect(() => SiteSchema.parse(missingSiteCity)).toThrow(ZodError);
    });

    it('rejects site with invalid field lengths', () => {
      const longSiteName = {
        ...validSiteBase,
        site_name: 'a'.repeat(256), // Exceeds 255 char limit
      };

      const longAddress = {
        ...validSiteBase,
        site_address: 'a'.repeat(256), // Exceeds 255 char limit
      };

      const longCity = {
        ...validSiteBase,
        site_city: 'a'.repeat(101), // Exceeds 100 char limit
      };

      expect(() => SiteSchema.parse(longSiteName)).toThrow(ZodError);
      expect(() => SiteSchema.parse(longAddress)).toThrow(ZodError);
      expect(() => SiteSchema.parse(longCity)).toThrow(ZodError);
    });

    it('validates field length limits correctly', () => {
      const maxLengthSite = {
        ...validSiteBase,
        site_name: 'a'.repeat(255),
        site_address: 'b'.repeat(255),
        site_address_2: 'c'.repeat(255),
        site_city: 'd'.repeat(100),
        site_state: 'e'.repeat(50),
        site_postal_code: 'f'.repeat(20),
        site_contact_name: 'g'.repeat(255),
        site_contact_phone: 'h'.repeat(50),
        site_contact_email: 'test@' + 'i'.repeat(245) + '.com', // 255 total
      };

      expect(() => SiteSchema.parse(maxLengthSite)).not.toThrow();
    });

    it('rejects invalid email format', () => {
      const invalidEmails = [
        'not-an-email',
        'missing@domain',
        '@missing-local.com',
        'spaces in@email.com',
        'double@@domain.com',
      ];

      invalidEmails.forEach((email) => {
        const siteWithInvalidEmail = {
          ...validSiteBase,
          site_contact_email: email,
        };
        expect(() => SiteSchema.parse(siteWithInvalidEmail)).toThrow(ZodError);
      });
    });

    it('validates various email formats', () => {
      const validEmails = [
        'test@example.com',
        'user.name@domain.co.uk',
        'first+last@subdomain.example.org',
        '123@numbers.com',
      ];

      validEmails.forEach((email) => {
        const siteWithValidEmail = {
          ...validSiteBase,
          site_contact_email: email,
        };
        expect(() => SiteSchema.parse(siteWithValidEmail)).not.toThrow();
      });
    });

    it('inherits BaseEntity properties correctly', () => {
      const site = SiteSchema.parse(validSiteBase);
      expect(site.id).toBe(validSiteBase.id);
      expect(site.created_at).toBeInstanceOf(Date);
      expect(site.updated_at).toBeInstanceOf(Date);
    });
  });

  describe('CreateSiteSchema', () => {
    it('validates site creation without system fields', () => {
      const createSiteData: CreateSite = {
        site_name: 'New Site',
        site_address: '456 New Street',
        site_city: 'New City',
        site_state: 'NC',
        site_postal_code: '54321',
        site_contact_name: 'Jane Smith',
        site_contact_phone: '555-987-6543',
        site_contact_email: 'jane.smith@newcompany.com',
      };

      expect(() => CreateSiteSchema.parse(createSiteData)).not.toThrow();
      const result = CreateSiteSchema.parse(createSiteData);
      expect(result.site_name).toBe(createSiteData.site_name);
      expect((result as Partial<Site>).id).toBeUndefined();
      expect((result as Partial<Site>).created_at).toBeUndefined();
    });

    it('validates minimal site creation', () => {
      const minimalSite: CreateSite = {
        site_name: 'Minimal Site',
        site_address: '789 Min Street',
        site_city: 'Min City',
        site_state: 'MC',
        site_postal_code: '98765',
      };

      expect(() => CreateSiteSchema.parse(minimalSite)).not.toThrow();
      const result = CreateSiteSchema.parse(minimalSite);
      expect(result.site_contact_name).toBeUndefined();
    });

    it('omits system-generated fields from creation data', () => {
      const siteWithSystemFields = {
        id: '550e8400-e29b-41d4-a716-446655440000',
        site_name: 'Site with ID',
        site_address: '123 ID Street',
        site_city: 'ID City',
        site_state: 'IC',
        site_postal_code: '11111',
        created_at: new Date(),
        updated_at: new Date(),
      };

      // Should not throw, but should omit system fields
      expect(() => CreateSiteSchema.parse(siteWithSystemFields)).not.toThrow();
      const result = CreateSiteSchema.parse(siteWithSystemFields);
      expect((result as Partial<Site>).id).toBeUndefined();
      expect((result as Partial<Site>).created_at).toBeUndefined();
      expect((result as Partial<Site>).updated_at).toBeUndefined();
      expect(result.site_name).toBe('Site with ID');
    });
  });

  describe('UpdateSiteSchema', () => {
    it('validates partial site updates', () => {
      const partialUpdates: UpdateSite[] = [
        { site_name: 'Updated Name' },
        { site_contact_email: 'new.email@company.com' },
        { site_address: 'New Address', site_city: 'New City' },
        {}, // Empty update should be valid
      ];

      partialUpdates.forEach((update) => {
        expect(() => UpdateSiteSchema.parse(update)).not.toThrow();
      });
    });

    it('validates complete site update', () => {
      const completeUpdate: UpdateSite = {
        site_name: 'Completely Updated Site',
        site_address: '999 Update Boulevard',
        site_address_2: 'Floor 10',
        site_city: 'Update City',
        site_state: 'UC',
        site_postal_code: '00000',
        site_contact_name: 'Updated Contact',
        site_contact_phone: '555-000-0000',
        site_contact_email: 'updated@contact.com',
      };

      expect(() => UpdateSiteSchema.parse(completeUpdate)).not.toThrow();
      const result = UpdateSiteSchema.parse(completeUpdate);
      expect(result.site_name).toBe(completeUpdate.site_name);
    });

    it('rejects invalid field values in updates', () => {
      const invalidUpdates = [
        { site_name: 'a'.repeat(256) }, // Too long
        { site_contact_email: 'invalid-email' }, // Invalid email
        { site_city: 'a'.repeat(101) }, // Too long
      ];

      invalidUpdates.forEach((update) => {
        expect(() => UpdateSiteSchema.parse(update)).toThrow(ZodError);
      });
    });

    it('allows null values for optional fields in updates', () => {
      const updateWithNulls: UpdateSite = {
        site_address_2: null,
        site_contact_name: null,
        site_contact_phone: null,
        site_contact_email: null,
      };

      expect(() => UpdateSiteSchema.parse(updateWithNulls)).not.toThrow();
      const result = UpdateSiteSchema.parse(updateWithNulls);
      expect(result.site_address_2).toBeNull();
    });
  });
});
