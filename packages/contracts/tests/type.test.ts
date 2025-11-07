import { describe, it, expect } from 'vitest';
import { ZodError } from 'zod';
import {
  TypeSchema,
  CreateTypeSchema,
  UpdateTypeSchema,
  type Type,
  type CreateType,
  type UpdateType,
} from '../src/schemas/existing/type.js';

describe('Type Schemas', () => {
  describe('TypeSchema', () => {
    const validTypeBase = {
      id: '550e8400-e29b-41d4-a716-446655440000',
      created_at: new Date('2023-01-15T10:30:00Z'),
      updated_at: new Date('2023-01-15T10:30:00Z'),
      name: 'Door',
    };

    it('validates a complete valid type', () => {
      const validType: Type = {
        ...validTypeBase,
        label_abbreviation: 'DR',
        color: '#FF5733',
        description: 'Various types of doors and entryways',
        deleted_at: null,
      };

      expect(() => TypeSchema.parse(validType)).not.toThrow();
      const result = TypeSchema.parse(validType);
      expect(result.name).toBe(validType.name);
      expect(result.color).toBe(validType.color);
      expect(result.description).toBe(validType.description);
    });

    it('validates type with minimal required fields', () => {
      expect(() => TypeSchema.parse(validTypeBase)).not.toThrow();
      const result = TypeSchema.parse(validTypeBase);
      expect(result.name).toBe(validTypeBase.name);
      expect(result.color).toBe('#808080'); // Default value
      expect(result.label_abbreviation).toBeUndefined();
    });

    it('applies default color when not provided', () => {
      const typeWithoutColor = { ...validTypeBase };

      const result = TypeSchema.parse(typeWithoutColor);
      expect(result.color).toBe('#808080');
    });

    it('validates type with null optional fields', () => {
      const typeWithNulls = {
        ...validTypeBase,
        label_abbreviation: null,
        description: null,
      };

      expect(() => TypeSchema.parse(typeWithNulls)).not.toThrow();
      const result = TypeSchema.parse(typeWithNulls);
      expect(result.label_abbreviation).toBeNull();
      expect(result.description).toBeNull();
    });

    it('rejects type with missing required fields', () => {
      const missingName = { ...validTypeBase };
      delete (missingName as Partial<typeof missingName>).name;

      expect(() => TypeSchema.parse(missingName)).toThrow(ZodError);
    });

    it('rejects type with invalid field lengths', () => {
      const longName = {
        ...validTypeBase,
        name: 'a'.repeat(256), // Exceeds 255 char limit
      };

      const longAbbreviation = {
        ...validTypeBase,
        label_abbreviation: 'a'.repeat(51), // Exceeds 50 char limit
      };

      const longColor = {
        ...validTypeBase,
        color: 'a'.repeat(51), // Exceeds 50 char limit
      };

      expect(() => TypeSchema.parse(longName)).toThrow(ZodError);
      expect(() => TypeSchema.parse(longAbbreviation)).toThrow(ZodError);
      expect(() => TypeSchema.parse(longColor)).toThrow(ZodError);
    });

    it('validates field length limits correctly', () => {
      const maxLengthType = {
        ...validTypeBase,
        name: 'a'.repeat(255),
        label_abbreviation: 'b'.repeat(50),
        color: 'c'.repeat(50),
        description: 'Long description'.repeat(100),
      };

      expect(() => TypeSchema.parse(maxLengthType)).not.toThrow();
    });

    it('validates various color formats', () => {
      const validColors = [
        '#FF5733',
        '#808080',
        'red',
        'blue',
        'rgb(255, 87, 51)',
        'rgba(255, 87, 51, 0.5)',
        'hsl(9, 100%, 60%)',
      ];

      validColors.forEach((color) => {
        const typeWithColor = {
          ...validTypeBase,
          color,
        };
        expect(() => TypeSchema.parse(typeWithColor)).not.toThrow();
      });
    });

    it('inherits BaseEntity properties correctly', () => {
      const type = TypeSchema.parse(validTypeBase);
      expect(type.id).toBe(validTypeBase.id);
      expect(type.created_at).toBeInstanceOf(Date);
      expect(type.updated_at).toBeInstanceOf(Date);
    });
  });

  describe('CreateTypeSchema', () => {
    it('validates type creation without system fields', () => {
      const createTypeData: CreateType = {
        name: 'Window',
        label_abbreviation: 'WIN',
        color: '#33A1FF',
        description: 'Various window types and configurations',
      };

      expect(() => CreateTypeSchema.parse(createTypeData)).not.toThrow();
      const result = CreateTypeSchema.parse(createTypeData);
      expect(result.name).toBe(createTypeData.name);
      expect((result as Partial<Type>).id).toBeUndefined();
      expect((result as Partial<Type>).created_at).toBeUndefined();
    });

    it('validates minimal type creation', () => {
      const minimalType = {
        name: 'Minimal Type',
      };

      expect(() => CreateTypeSchema.parse(minimalType)).not.toThrow();
      const result = CreateTypeSchema.parse(minimalType);
      expect(result.color).toBe('#808080'); // Default should be applied
      expect(result.label_abbreviation).toBeUndefined();
    });

    it('omits system-generated fields from creation data', () => {
      const typeWithSystemFields = {
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Type with System Fields',
        created_at: new Date(),
        updated_at: new Date(),
        deleted_at: null,
      };

      expect(() => CreateTypeSchema.parse(typeWithSystemFields)).not.toThrow();
      const result = CreateTypeSchema.parse(typeWithSystemFields);
      expect((result as Partial<Type>).id).toBeUndefined();
      expect((result as Partial<Type>).created_at).toBeUndefined();
      expect(result.name).toBe('Type with System Fields');
    });
  });

  describe('UpdateTypeSchema', () => {
    it('validates partial type updates', () => {
      const partialUpdates: UpdateType[] = [
        { name: 'Updated Name' },
        { color: '#FF0000' },
        { description: 'Updated description' },
        { label_abbreviation: 'UPD' },
        {}, // Empty update should be valid
      ];

      partialUpdates.forEach((update) => {
        expect(() => UpdateTypeSchema.parse(update)).not.toThrow();
      });
    });

    it('validates complete type update', () => {
      const completeUpdate: UpdateType = {
        name: 'Completely Updated Type',
        label_abbreviation: 'CUT',
        color: '#00FF00',
        description: 'Completely updated type description',
      };

      expect(() => UpdateTypeSchema.parse(completeUpdate)).not.toThrow();
      const result = UpdateTypeSchema.parse(completeUpdate);
      expect(result.name).toBe(completeUpdate.name);
      expect(result.color).toBe(completeUpdate.color);
    });

    it('rejects invalid field values in updates', () => {
      const invalidUpdates = [
        { name: 'a'.repeat(256) }, // Too long
        { label_abbreviation: 'a'.repeat(51) }, // Too long
        { color: 'a'.repeat(51) }, // Too long
      ];

      invalidUpdates.forEach((update) => {
        expect(() => UpdateTypeSchema.parse(update)).toThrow(ZodError);
      });
    });

    it('allows null values for optional fields in updates', () => {
      const updateWithNulls: UpdateType = {
        label_abbreviation: null,
        description: null,
      };

      expect(() => UpdateTypeSchema.parse(updateWithNulls)).not.toThrow();
      const result = UpdateTypeSchema.parse(updateWithNulls);
      expect(result.label_abbreviation).toBeNull();
      expect(result.description).toBeNull();
    });

    it('maintains default color behavior in updates', () => {
      const updateWithoutColor: UpdateType = {
        name: 'Updated Name Only',
      };

      const result = UpdateTypeSchema.parse(updateWithoutColor);
      // Color should not be set since it's a partial update
      expect(result.color).toBeUndefined();

      // But when creating a full type, default should apply
      const fullUpdate: UpdateType = {
        name: 'Full Update',
        label_abbreviation: 'FU',
        description: 'Full update description',
      };

      const fullResult = UpdateTypeSchema.parse(fullUpdate);
      expect(fullResult.color).toBeUndefined(); // Partial update doesn't apply defaults
    });
  });
});
