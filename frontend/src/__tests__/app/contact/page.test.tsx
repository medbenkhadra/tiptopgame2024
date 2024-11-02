import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import Contact from '@/app/contact/page';
import '@testing-library/jest-dom';

describe('Contact', () => {
    test('renders Contact Page content correctly', async () => {
        render(<Contact />);

        const loadingSpinner = screen.getByTestId('loading-spinner');
        expect(loadingSpinner).toBeInTheDocument();

        await waitFor(() => {
            const loadingSpinner = screen.queryByTestId('loading-spinner');
            expect(loadingSpinner).not.toBeInTheDocument();
        });

        const contactSection = screen.getByTestId('contact-section');
        expect(contactSection).toBeInTheDocument();


        const nameInput = screen.getByPlaceholderText('Nom et prénom');
        expect(nameInput).toBeInTheDocument();

        const emailInput = screen.getByPlaceholderText('E-mail');
        expect(emailInput).toBeInTheDocument();

        const subjectInput = screen.getByPlaceholderText('Sujet');
        expect(subjectInput).toBeInTheDocument();

        const messageTextarea = screen.getByPlaceholderText('Votre message...');
        expect(messageTextarea).toBeInTheDocument();

        const sendButton = screen.getByRole('button', { name: /envoyer votre message/i });
        expect(sendButton).toBeInTheDocument();

        fireEvent.change(nameInput, { target: { value: 'John Doe' } });
        fireEvent.change(emailInput, { target: { value: 'john@example.com' } });
        fireEvent.change(subjectInput, { target: { value: 'Test subject' } });
        fireEvent.change(messageTextarea, { target: { value: 'Test message' } });



        fireEvent.click(sendButton);


    });
});
