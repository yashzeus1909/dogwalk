import Header from "@/components/header";
import BottomNavigation from "@/components/bottom-navigation";
import { Card, CardContent } from "@/components/ui/card";
import { MessageCircle } from "lucide-react";

export default function Messages() {
  return (
    <div className="min-h-screen bg-neutral-50">
      <Header />
      
      <main className="px-4 py-6">
        <div className="mb-6">
          <h1 className="text-2xl font-bold text-neutral-900">Messages</h1>
          <p className="text-neutral-600 mt-1">Chat with your dog walkers</p>
        </div>

        <Card>
          <CardContent className="p-8 text-center">
            <MessageCircle className="w-12 h-12 text-neutral-400 mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-neutral-900 mb-2">No messages yet</h3>
            <p className="text-neutral-600">Start a conversation with your dog walkers after booking a service!</p>
          </CardContent>
        </Card>
      </main>

      <BottomNavigation />
    </div>
  );
}