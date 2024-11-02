import { render, screen, waitFor , act } from '@testing-library/react';
import Home from '@/app/page';
import '@testing-library/jest-dom';

jest.useFakeTimers();
window.scrollTo = jest.fn();

describe('Home', () => {
    test('rendering Home Page', async () => {

        render(<Home />);

        expect(screen.queryByTestId('landing-page-top-section')).toBeNull();
        expect(screen.getByTestId('loading-spinner')).toBeInTheDocument();

        act(() => {
            jest.advanceTimersByTime(500);
        });

        await waitFor(() => {
            expect(screen.queryByTestId('loading-spinner')).toBeNull();
            expect(screen.getByTestId('landing-page-top-section')).toBeInTheDocument();
        });






    });
});
