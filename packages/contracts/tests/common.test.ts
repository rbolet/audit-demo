import { describe, it, expect } from 'vitest';
import { ZodError } from 'zod';
import {
  UuidSchema,
  TimestampsSchema,
  AssessmentStatusSchema,
  DataTypeSchema,
  BaseEntitySchema,
  type AssessmentStatus,
  type DataType,
} from '../src/schemas/common.js';

describe('Common Schemas', () => {
  describe('UuidSchema', () => {
    it('validates correct UUID format', () => {
      const validUuids = [
        '550e8400-e29b-41d4-a716-446655440000',
        'f47ac10b-58cc-4372-a567-0e02b2c3d479',
        '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
      ];

      validUuids.forEach((uuid) => {
        expect(() => UuidSchema.parse(uuid)).not.toThrow();
        expect(UuidSchema.parse(uuid)).toBe(uuid);
      });
    });

    it('rejects invalid UUID formats', () => {
      const invalidUuids = [
        'not-a-uuid',
        '550e8400-e29b-41d4-a716',
        '550e8400-e29b-41d4-a716-446655440000-extra',
        '',
        null,
        undefined,
        123,
      ];

      invalidUuids.forEach((uuid) => {
        expect(() => UuidSchema.parse(uuid)).toThrow(ZodError);
      });
    });
  });

  describe('TimestampsSchema', () => {
    it('validates correct timestamp object', () => {
      const validTimestamps = {
        created_at: new Date('2023-01-15T10:30:00Z'),
        updated_at: new Date('2023-01-15T10:30:00Z'),
        deleted_at: null,
      };

      expect(() => TimestampsSchema.parse(validTimestamps)).not.toThrow();
      const result = TimestampsSchema.parse(validTimestamps);
      expect(result.created_at).toBeInstanceOf(Date);
      expect(result.updated_at).toBeInstanceOf(Date);
      expect(result.deleted_at).toBeNull();
    });

    it('coerces string dates to Date objects', () => {
      const timestampsWithStrings = {
        created_at: '2023-01-15T10:30:00Z',
        updated_at: '2023-01-15T10:30:00Z',
      };

      const result = TimestampsSchema.parse(timestampsWithStrings);
      expect(result.created_at).toBeInstanceOf(Date);
      expect(result.updated_at).toBeInstanceOf(Date);
    });

    it('handles optional deleted_at field', () => {
      const withoutDeletedAt = {
        created_at: new Date('2023-01-15T10:30:00Z'),
        updated_at: new Date('2023-01-15T10:30:00Z'),
      };

      const withDeletedAt = {
        created_at: new Date('2023-01-15T10:30:00Z'),
        updated_at: new Date('2023-01-15T10:30:00Z'),
        deleted_at: new Date('2023-01-16T10:30:00Z'),
      };

      expect(() => TimestampsSchema.parse(withoutDeletedAt)).not.toThrow();
      expect(() => TimestampsSchema.parse(withDeletedAt)).not.toThrow();

      const result1 = TimestampsSchema.parse(withoutDeletedAt);
      const result2 = TimestampsSchema.parse(withDeletedAt);

      expect(result1.deleted_at).toBeUndefined();
      expect(result2.deleted_at).toBeInstanceOf(Date);
    });

    it('rejects invalid timestamp formats', () => {
      const invalidTimestamps = [
        { created_at: 'invalid-date', updated_at: new Date() },
        { created_at: new Date(), updated_at: 'invalid-date' },
        { created_at: new Date(), updated_at: new Date(), deleted_at: 'invalid-date' },
      ];

      invalidTimestamps.forEach((timestamps) => {
        expect(() => TimestampsSchema.parse(timestamps)).toThrow(ZodError);
      });
    });
  });

  describe('AssessmentStatusSchema', () => {
    it('validates all allowed assessment statuses', () => {
      const validStatuses: AssessmentStatus[] = [
        'PLANNED',
        'ASSIGNED',
        'IN_PROGRESS',
        'IN_QC',
        'COMPLETE',
      ];

      validStatuses.forEach((status) => {
        expect(() => AssessmentStatusSchema.parse(status)).not.toThrow();
        expect(AssessmentStatusSchema.parse(status)).toBe(status);
      });
    });

    it('rejects invalid assessment statuses', () => {
      const invalidStatuses = [
        'INVALID_STATUS',
        'planned',
        'assigned',
        'Draft',
        '',
        null,
        undefined,
        123,
      ];

      invalidStatuses.forEach((status) => {
        expect(() => AssessmentStatusSchema.parse(status)).toThrow(ZodError);
      });
    });

    it('provides proper type inference', () => {
      const status = AssessmentStatusSchema.parse('IN_PROGRESS');
      // TypeScript should infer this as AssessmentStatus type
      expect(typeof status).toBe('string');
      expect(status).toBe('IN_PROGRESS');
    });
  });

  describe('DataTypeSchema', () => {
    it('validates all allowed data types', () => {
      const validDataTypes: DataType[] = ['NUMBER', 'CHARACTERS'];

      validDataTypes.forEach((dataType) => {
        expect(() => DataTypeSchema.parse(dataType)).not.toThrow();
        expect(DataTypeSchema.parse(dataType)).toBe(dataType);
      });
    });

    it('rejects invalid data types', () => {
      const invalidDataTypes = [
        'STRING',
        'INTEGER',
        'number',
        'characters',
        'Boolean',
        '',
        null,
        undefined,
        123,
      ];

      invalidDataTypes.forEach((dataType) => {
        expect(() => DataTypeSchema.parse(dataType)).toThrow(ZodError);
      });
    });
  });

  describe('BaseEntitySchema', () => {
    it('validates complete base entity', () => {
      const validEntity = {
        id: '550e8400-e29b-41d4-a716-446655440000',
        created_at: new Date('2023-01-15T10:30:00Z'),
        updated_at: new Date('2023-01-15T10:30:00Z'),
        deleted_at: null,
      };

      expect(() => BaseEntitySchema.parse(validEntity)).not.toThrow();
      const result = BaseEntitySchema.parse(validEntity);
      expect(result.id).toBe(validEntity.id);
      expect(result.created_at).toBeInstanceOf(Date);
      expect(result.updated_at).toBeInstanceOf(Date);
      expect(result.deleted_at).toBeNull();
    });

    it('validates base entity without deleted_at', () => {
      const entityWithoutDeletedAt = {
        id: '550e8400-e29b-41d4-a716-446655440000',
        created_at: new Date('2023-01-15T10:30:00Z'),
        updated_at: new Date('2023-01-15T10:30:00Z'),
      };

      expect(() => BaseEntitySchema.parse(entityWithoutDeletedAt)).not.toThrow();
      const result = BaseEntitySchema.parse(entityWithoutDeletedAt);
      expect(result.deleted_at).toBeUndefined();
    });

    it('rejects base entity with invalid UUID', () => {
      const invalidEntity = {
        id: 'not-a-uuid',
        created_at: new Date('2023-01-15T10:30:00Z'),
        updated_at: new Date('2023-01-15T10:30:00Z'),
      };

      expect(() => BaseEntitySchema.parse(invalidEntity)).toThrow(ZodError);
    });

    it('rejects base entity with missing required fields', () => {
      const missingId = {
        created_at: new Date('2023-01-15T10:30:00Z'),
        updated_at: new Date('2023-01-15T10:30:00Z'),
      };

      const missingCreatedAt = {
        id: '550e8400-e29b-41d4-a716-446655440000',
        updated_at: new Date('2023-01-15T10:30:00Z'),
      };

      expect(() => BaseEntitySchema.parse(missingId)).toThrow(ZodError);
      expect(() => BaseEntitySchema.parse(missingCreatedAt)).toThrow(ZodError);
    });

    it('handles string date coercion', () => {
      const entityWithStringDates = {
        id: '550e8400-e29b-41d4-a716-446655440000',
        created_at: '2023-01-15T10:30:00Z',
        updated_at: '2023-01-15T10:30:00Z',
        deleted_at: '2023-01-16T10:30:00Z',
      };

      const result = BaseEntitySchema.parse(entityWithStringDates);
      expect(result.created_at).toBeInstanceOf(Date);
      expect(result.updated_at).toBeInstanceOf(Date);
      expect(result.deleted_at).toBeInstanceOf(Date);
    });
  });
});
