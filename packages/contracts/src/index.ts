/**
 * Shared validation schemas and types for audit-demo
 *
 * This package provides:
 * - Zod schemas for runtime validation
 * - TypeScript types inferred from schemas
 * - JSON Schema generation for backend validation
 */

// Common schemas
export * from './schemas/common.js';

// Assessment domain
export * from './schemas/assessment/site.js';
export * from './schemas/assessment/assessment.js';

// Existing conditions domain
export * from './schemas/existing/type.js';
export * from './schemas/existing/attribute.js';
export * from './schemas/existing/type-attribute.js';
export * from './schemas/existing/location.js';
export * from './schemas/existing/existing.js';
export * from './schemas/existing/existing-attribute-value.js';

// Valueset domain
export * from './schemas/valueset/valueset.js';
export * from './schemas/valueset/valueset-value.js';
export * from './schemas/valueset/attribute-valueset.js';

export * from './utils/schema-generator.js';
