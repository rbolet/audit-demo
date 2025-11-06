import { zodToJsonSchema } from 'zod-to-json-schema';
import type { ZodSchema } from 'zod';

/**
 * Converts a Zod schema to JSON Schema
 * Useful for generating Laravel validation rules or OpenAPI specs
 *
 * @param schema - Zod schema to convert
 * @param name - Optional name for the schema (used in $ref)
 * @returns JSON Schema object
 */
export function toJsonSchema(schema: ZodSchema, name?: string) {
  return zodToJsonSchema(schema, name);
}

/**
 * Helper to generate all JSON schemas for Laravel
 * Can be expanded to write schemas to a file for backend consumption
 */
export function generateAllSchemas() {
  // This can be used in a build script to generate schemas
  // For now, it's a placeholder for future expansion
  return {
    // Add schemas here as they're created
    // example: toJsonSchema(exampleSchema, 'Example'),
  };
}
