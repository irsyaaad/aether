import React from 'react';

export default function Home({ name }) {
    return (
        <div style={{ padding: '2rem', fontFamily: 'sans-serif' }}>
            <h1>Hello from Inertia & React!</h1>
            <p>Welcome, {name || 'User'}!</p>
            <p>This is your first React component located at <code>resources/js/Pages/Home.jsx</code>.</p>
        </div>
    );
}
