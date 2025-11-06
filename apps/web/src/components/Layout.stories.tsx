import type { Meta, StoryObj } from '@storybook/react';
import Layout from './Layout';
import Typography from '@mui/material/Typography';

const meta = {
  title: 'Components/Layout',
  component: Layout,
  parameters: {
    layout: 'fullscreen',
  },
  tags: ['autodocs'],
} satisfies Meta<typeof Layout>;

export default meta;
type Story = StoryObj<typeof meta>;

export const Default: Story = {
  args: {
    children: (
      <Typography variant="h4" sx={{ p: 4 }}>
        Page Content
      </Typography>
    ),
  },
};

export const WithMultipleElements: Story = {
  args: {
    children: (
      <div style={{ padding: '2rem' }}>
        <Typography variant="h3" gutterBottom>
          Welcome
        </Typography>
        <Typography variant="body1" paragraph>
          This is an example of the layout with multiple content elements.
        </Typography>
        <Typography variant="body2" color="text.secondary">
          The layout includes the app bar and main content area.
        </Typography>
      </div>
    ),
  },
};
