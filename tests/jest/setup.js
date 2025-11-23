/**
 * Jest setup file
 */

// Mock WordPress i18n
global.__ = ( text ) => text;

// Mock DOM API
global.document = {
	addEventListener: jest.fn(),
	getElementById: jest.fn(),
};

// Mock localStorage
const localStorageMock = {
	getItem: jest.fn(),
	setItem: jest.fn(),
	removeItem: jest.fn(),
	clear: jest.fn(),
};
global.localStorage = localStorageMock;

// Mock fetch API
global.fetch = jest.fn();

// Mock window.location
delete global.window.location;
global.window = Object.create( window );
global.window.location = {
	origin: 'http://localhost',
};
