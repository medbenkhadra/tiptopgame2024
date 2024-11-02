import NextAuth from 'next-auth';
import { Provider } from 'next-auth/providers';

export default NextAuth({
    providers: [
        Provider.Facebook({
            clientId: process.env.FACEBOOK_CLIENT_ID,
            clientSecret: process.env.FACEBOOK_CLIENT_SECRET,
            scope: 'email', // Specify the requested permissions
        }),
    ],
    // Add additional configurations as needed
});
