# @audit-demo/contracts

Shared validation schemas and TypeScript types for the audit-demo monorepo.

## Purpose

This package serves as the single source of truth for data validation across the frontend and backend applications. It uses Zod for schema definition, which provides:

- Runtime validation for the frontend
- TypeScript type inference
- JSON Schema generation for backend validation

## Usage

### Frontend (TypeScript/React)

```typescript
import { exampleSchema, type Example } from '@audit-demo/contracts';

// Runtime validation
const result = exampleSchema.safeParse(data);
if (result.success) {
  const validated: Example = result.data;
}

// Type-only usage
const myExample: Example = {
  id: 1,
  name: 'Test',
  createdAt: new Date().toISOString(),
  updatedAt: new Date().toISOString(),
};
```

### Backend (Laravel/PHP)

JSON schemas can be generated and used for validation. See `src/utils/schema-generator.ts` for implementation details.

## Development

```bash
# Build the package
pnpm build

# Watch mode for development
pnpm dev

# Type checking
pnpm type-check

# Linting
pnpm lint
```
