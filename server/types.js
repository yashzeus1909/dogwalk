declare module 'express-session' {
  interface SessionData {
    userId?: number;
    user?: {
      id: number;
      name: string;
      email: string;
      role: string;
    };
  }
}

export {};