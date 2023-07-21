// assets/js/app.tsx
import * as React from 'react';
import * as ReactDOM from 'react-dom';

const App: React.FC = () => {
    return (
        <div className="App">
            <h1>Hello, React!</h1>
        </div>
    );
}

ReactDOM.render(<App />, document.getElementById('root'));
