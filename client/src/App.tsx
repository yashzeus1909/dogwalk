import { Switch, Route } from "wouter";
import { queryClient } from "./lib/queryClient";
import { QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import Home from "@/pages/home";
import Bookings from "@/pages/bookings";
import Messages from "@/pages/messages";
import Profile from "@/pages/profile";
import Admin from "@/pages/admin";
import EditWalker from "@/pages/edit-walker";
import NotFound from "@/pages/not-found";

function Router() {
  return (
    <Switch>
      <Route path="/" component={Home} />
      <Route path="/bookings" component={Bookings} />
      <Route path="/messages" component={Messages} />
      <Route path="/profile" component={Profile} />
      <Route path="/admin" component={Admin} />
      <Route path="/edit-walker/:id" component={EditWalker} />
      <Route component={NotFound} />
    </Switch>
  );
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <Toaster />
        <Router />
      </TooltipProvider>
    </QueryClientProvider>
  );
}

export default App;
