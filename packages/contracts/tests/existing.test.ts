import { describe, it, expect } from 'vitest';
import { ZodError } from 'zod';
import {
  ExistingSchema,
  CreateExistingSchema,
  UpdateExistingSchema,
  type Existing,
  type CreateExisting,
  type UpdateExisting,
} from '../src/schemas/existing/existing.js';

describe('Existing Schemas', () => {
  describe('ExistingSchema', () => {
    const validExistingBase = {
      id: '550e8400-e29b-41d4-a716-446655440000',
      created_at: new Date('2023-01-15T10:30:00Z'),
      updated_at: new Date('2023-01-15T10:30:00Z'),
      type_id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
      location_id: '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
      label: 'DR-001',
    };

    it('validates a complete valid existing item', () => {
      const validExisting: Existing = {
        ...validExistingBase,
        name: 'Main Entrance Door',
        label_abbr: 'MED',
        attribute_values_hash: 'abc123def456',
        quantity: 1,
        notes: 'Primary entrance door with automatic closer',
        deleted_at: null,
      };

      expect(() => ExistingSchema.parse(validExisting)).not.toThrow();
      const result = ExistingSchema.parse(validExisting);
      expect(result.name).toBe(validExisting.name);
      expect(result.type_id).toBe(validExisting.type_id);
      expect(result.quantity).toBe(validExisting.quantity);
    });

    it('validates existing item with minimal required fields', () => {
      expect(() => ExistingSchema.parse(validExistingBase)).not.toThrow();
      const result = ExistingSchema.parse(validExistingBase);
      expect(result.label).toBe(validExistingBase.label);
      expect(result.quantity).toBe(1); // Default value
      expect(result.name).toBeUndefined();
    });

    it('applies default quantity when not provided', () => {
      const existingWithoutQuantity = { ...validExistingBase };

      const result = ExistingSchema.parse(existingWithoutQuantity);
      expect(result.quantity).toBe(1);
    });

    it('validates existing item with null optional fields', () => {
      const existingWithNulls = {
        ...validExistingBase,
        name: null,
        label_abbr: null,
        attribute_values_hash: null,
        notes: null,
      };

      expect(() => ExistingSchema.parse(existingWithNulls)).not.toThrow();
      const result = ExistingSchema.parse(existingWithNulls);
      expect(result.name).toBeNull();
      expect(result.label_abbr).toBeNull();
    });

    it('rejects existing item with missing required fields', () => {
      const missingTypeId = { ...validExistingBase };
      delete (missingTypeId as Partial<typeof missingTypeId>).type_id;

      const missingLocationId = { ...validExistingBase };
      delete (missingLocationId as Partial<typeof missingLocationId>).location_id;

      const missingLabel = { ...validExistingBase };
      delete (missingLabel as Partial<typeof missingLabel>).label;

      expect(() => ExistingSchema.parse(missingTypeId)).toThrow(ZodError);
      expect(() => ExistingSchema.parse(missingLocationId)).toThrow(ZodError);
      expect(() => ExistingSchema.parse(missingLabel)).toThrow(ZodError);
    });

    it('rejects existing item with invalid UUID formats', () => {
      const invalidTypeId = {
        ...validExistingBase,
        type_id: 'not-a-uuid',
      };

      const invalidLocationId = {
        ...validExistingBase,
        location_id: 'not-a-uuid',
      };

      expect(() => ExistingSchema.parse(invalidTypeId)).toThrow(ZodError);
      expect(() => ExistingSchema.parse(invalidLocationId)).toThrow(ZodError);
    });

    it('rejects existing item with invalid field lengths', () => {
      const longName = {
        ...validExistingBase,
        name: 'a'.repeat(256), // Exceeds 255 char limit
      };

      const longLabel = {
        ...validExistingBase,
        label: 'b'.repeat(256), // Exceeds 255 char limit
      };

      const longLabelAbbr = {
        ...validExistingBase,
        label_abbr: 'c'.repeat(101), // Exceeds 100 char limit
      };

      expect(() => ExistingSchema.parse(longName)).toThrow(ZodError);
      expect(() => ExistingSchema.parse(longLabel)).toThrow(ZodError);
      expect(() => ExistingSchema.parse(longLabelAbbr)).toThrow(ZodError);
    });

    it('validates field length limits correctly', () => {
      const maxLengthExisting = {
        ...validExistingBase,
        name: 'a'.repeat(255),
        label: 'b'.repeat(255),
        label_abbr: 'c'.repeat(100),
        attribute_values_hash: 'd'.repeat(255),
        notes: 'e'.repeat(1000), // No limit specified
      };

      expect(() => ExistingSchema.parse(maxLengthExisting)).not.toThrow();
    });

    it('validates quantity constraints', () => {
      const validQuantities = [1, 5, 10, 100, 999];

      validQuantities.forEach((quantity) => {
        const existingWithQuantity = {
          ...validExistingBase,
          quantity,
        };
        expect(() => ExistingSchema.parse(existingWithQuantity)).not.toThrow();
      });
    });

    it('rejects invalid quantity values', () => {
      const invalidQuantities = [0, -1, -10, 1.5, 'not-a-number'];

      invalidQuantities.forEach((quantity) => {
        const existingWithInvalidQuantity = {
          ...validExistingBase,
          quantity,
        };
        expect(() => ExistingSchema.parse(existingWithInvalidQuantity)).toThrow(ZodError);
      });
    });

    it('inherits BaseEntity properties correctly', () => {
      const existing = ExistingSchema.parse(validExistingBase);
      expect(existing.id).toBe(validExistingBase.id);
      expect(existing.created_at).toBeInstanceOf(Date);
      expect(existing.updated_at).toBeInstanceOf(Date);
    });
  });

  describe('CreateExistingSchema', () => {
    it('validates existing item creation without system fields', () => {
      const createExistingData: CreateExisting = {
        type_id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
        location_id: '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
        name: 'New Door',
        label_abbr: 'ND',
        quantity: 2,
        notes: 'Newly installed door',
      };

      expect(() => CreateExistingSchema.parse(createExistingData)).not.toThrow();
      const result = CreateExistingSchema.parse(createExistingData);
      expect(result.type_id).toBe(createExistingData.type_id);
      expect((result as Partial<Existing>).id).toBeUndefined();
      expect((result as Partial<Existing>).label).toBeUndefined(); // Omitted
    });

    it('validates minimal existing item creation', () => {
      const minimalExisting = {
        type_id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
        location_id: '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
      };

      expect(() => CreateExistingSchema.parse(minimalExisting)).not.toThrow();
      const result = CreateExistingSchema.parse(minimalExisting);
      expect(result.quantity).toBe(1); // Default should be applied
      expect(result.name).toBeUndefined();
    });

    it('omits system-generated fields from creation data', () => {
      const existingWithSystemFields = {
        id: '550e8400-e29b-41d4-a716-446655440000',
        type_id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
        location_id: '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
        label: 'DR-001', // This should be omitted
        attribute_values_hash: 'abc123', // This should be omitted
        created_at: new Date(),
        updated_at: new Date(),
      };

      expect(() => CreateExistingSchema.parse(existingWithSystemFields)).not.toThrow();
      const result = CreateExistingSchema.parse(existingWithSystemFields);
      expect((result as Partial<Existing>).id).toBeUndefined();
      expect((result as Partial<Existing>).label).toBeUndefined();
      expect((result as Partial<Existing>).attribute_values_hash).toBeUndefined();
      expect(result.type_id).toBe(existingWithSystemFields.type_id);
    });
  });

  describe('UpdateExistingSchema', () => {
    it('validates partial existing item updates', () => {
      const partialUpdates: UpdateExisting[] = [
        { name: 'Updated Name' },
        { quantity: 5 },
        { notes: 'Updated notes' },
        { label_abbr: 'UPD' },
        {}, // Empty update should be valid
      ];

      partialUpdates.forEach((update) => {
        expect(() => UpdateExistingSchema.parse(update)).not.toThrow();
      });
    });

    it('validates complete existing item update', () => {
      const completeUpdate: UpdateExisting = {
        type_id: '550e8400-e29b-41d4-a716-446655440001',
        location_id: 'f47ac10b-58cc-4372-a567-0e02b2c3d480',
        name: 'Completely Updated Item',
        label_abbr: 'CUI',
        quantity: 3,
        notes: 'Completely updated notes',
      };

      expect(() => UpdateExistingSchema.parse(completeUpdate)).not.toThrow();
      const result = UpdateExistingSchema.parse(completeUpdate);
      expect(result.name).toBe(completeUpdate.name);
      expect(result.quantity).toBe(completeUpdate.quantity);
    });

    it('rejects invalid field values in updates', () => {
      const invalidUpdates = [
        { name: 'a'.repeat(256) }, // Too long
        { label_abbr: 'a'.repeat(101) }, // Too long
        { quantity: 0 }, // Below minimum
        { quantity: -5 }, // Negative
        { type_id: 'invalid-uuid' }, // Invalid UUID
        { location_id: 'invalid-uuid' }, // Invalid UUID
      ];

      invalidUpdates.forEach((update) => {
        expect(() => UpdateExistingSchema.parse(update)).toThrow(ZodError);
      });
    });

    it('allows null values for optional fields in updates', () => {
      const updateWithNulls: UpdateExisting = {
        name: null,
        label_abbr: null,
        notes: null,
      };

      expect(() => UpdateExistingSchema.parse(updateWithNulls)).not.toThrow();
      const result = UpdateExistingSchema.parse(updateWithNulls);
      expect(result.name).toBeNull();
      expect(result.label_abbr).toBeNull();
      expect(result.notes).toBeNull();
    });

    it('maintains default quantity behavior in updates', () => {
      const updateWithoutQuantity: UpdateExisting = {
        name: 'Updated Name Only',
      };

      const result = UpdateExistingSchema.parse(updateWithoutQuantity);
      // Quantity should not be set since it's a partial update
      expect(result.quantity).toBeUndefined();
    });
  });
});
