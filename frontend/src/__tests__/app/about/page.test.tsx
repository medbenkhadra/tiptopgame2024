import { render, screen, waitFor , act } from '@testing-library/react';
import About from '@/app/about/page';
import '@testing-library/jest-dom';

describe('About', () => {
    test('renders About Page content correctly', async () => {
        render(<About />);

        const loadingSpinner = screen.getByTestId('loading-spinner');
        expect(loadingSpinner).toBeInTheDocument();

        await waitFor(() => {
            const loadingSpinner = screen.queryByTestId('loading-spinner');
            expect(loadingSpinner).not.toBeInTheDocument();
        });

        const aboutSection = screen.getByTestId('about-section');
        expect(aboutSection).toBeInTheDocument();


    });
});