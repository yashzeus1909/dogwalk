import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import type { Booking, Walker } from "@shared/schema";
import Header from "@/components/header";
import BottomNavigation from "@/components/bottom-navigation";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useToast } from "@/hooks/use-toast";
import { apiRequest } from "@/lib/queryClient";
import { CalendarDays, Clock, MapPin, Phone, Mail, Settings } from "lucide-react";

export default function Bookings() {
  const { toast } = useToast();
  const queryClient = useQueryClient();
  
  const { data: bookings = [], isLoading } = useQuery<Booking[]>({
    queryKey: ['/api/bookings'],
  });

  const { data: walkers = [] } = useQuery<Walker[]>({
    queryKey: ['/api/walkers'],
  });

  const updateStatusMutation = useMutation({
    mutationFn: async ({ bookingId, status }: { bookingId: number; status: string }) => {
      const response = await apiRequest("PATCH", `/api/bookings/${bookingId}/status`, { status });
      return response.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/bookings'] });
      toast({
        title: "Status Updated",
        description: "Booking status has been updated and customer has been notified via email.",
      });
    },
    onError: () => {
      toast({
        title: "Update Failed",
        description: "Failed to update booking status.",
        variant: "destructive",
      });
    },
  });

  const testEmailMutation = useMutation({
    mutationFn: async (bookingId: number) => {
      const response = await apiRequest("POST", "/api/test-email", { bookingId });
      return response.json();
    },
    onSuccess: (data) => {
      toast({
        title: data.success ? "Email Sent" : "Email Failed",
        description: data.message,
        variant: data.success ? "default" : "destructive",
      });
    },
    onError: () => {
      toast({
        title: "Email Test Failed",
        description: "Failed to send test email.",
        variant: "destructive",
      });
    },
  });

  const getWalkerName = (walkerId: number) => {
    const walker = walkers.find(w => w.id === walkerId);
    return walker?.name || "Unknown Walker";
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'confirmed': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'completed': return 'bg-blue-100 text-blue-800';
      case 'cancelled': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const formatPrice = (cents: number) => {
    return `$${(cents / 100).toFixed(2)}`;
  };

  const handleStatusChange = (bookingId: number, newStatus: string) => {
    updateStatusMutation.mutate({ bookingId, status: newStatus });
  };

  const handleTestEmail = (bookingId: number) => {
    testEmailMutation.mutate(bookingId);
  };

  if (isLoading) {
    return (
      <div className="min-h-screen bg-neutral-50">
        <Header />
        <main className="px-4 py-6">
          <div className="space-y-4">
            {[...Array(3)].map((_, i) => (
              <Card key={i} className="animate-pulse">
                <CardContent className="p-4">
                  <div className="space-y-3">
                    <div className="h-4 bg-gray-200 rounded w-1/3"></div>
                    <div className="h-3 bg-gray-200 rounded w-1/2"></div>
                    <div className="h-3 bg-gray-200 rounded w-full"></div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </main>
        <BottomNavigation />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-neutral-50">
      <Header />
      
      <main className="px-4 py-6">
        <div className="mb-6">
          <h1 className="text-2xl font-bold text-neutral-900">My Bookings</h1>
          <p className="text-neutral-600 mt-1">Track your dog walking appointments</p>
        </div>

        {bookings.length === 0 ? (
          <Card>
            <CardContent className="p-8 text-center">
              <CalendarDays className="w-12 h-12 text-neutral-400 mx-auto mb-4" />
              <h3 className="text-lg font-semibold text-neutral-900 mb-2">No bookings yet</h3>
              <p className="text-neutral-600">Book your first dog walking service to get started!</p>
            </CardContent>
          </Card>
        ) : (
          <div className="space-y-4">
            {bookings.map((booking) => (
              <Card key={booking.id} className="overflow-hidden">
                <CardContent className="p-4">
                  <div className="flex items-start justify-between mb-3">
                    <div>
                      <h3 className="font-semibold text-neutral-900">
                        {getWalkerName(booking.walkerId)}
                      </h3>
                      <p className="text-sm text-neutral-600">for {booking.dogName}</p>
                    </div>
                    <Badge className={getStatusColor(booking.status)}>
                      {booking.status}
                    </Badge>
                  </div>

                  <div className="space-y-2 text-sm text-neutral-600">
                    <div className="flex items-center space-x-2">
                      <CalendarDays className="w-4 h-4" />
                      <span>{booking.date}</span>
                    </div>
                    <div className="flex items-center space-x-2">
                      <Clock className="w-4 h-4" />
                      <span>{booking.time} • {booking.duration} minutes</span>
                    </div>
                    <div className="flex items-center space-x-2">
                      <MapPin className="w-4 h-4" />
                      <span>{booking.dogSize} dog</span>
                    </div>
                    <div className="flex items-center space-x-2">
                      <Phone className="w-4 h-4" />
                      <span>{booking.phone}</span>
                    </div>
                  </div>

                  {booking.instructions && (
                    <div className="mt-3 p-3 bg-neutral-50 rounded-lg">
                      <p className="text-sm text-neutral-700">
                        <strong>Special instructions:</strong> {booking.instructions}
                      </p>
                    </div>
                  )}

                  <div className="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span className="text-sm text-neutral-600">Total paid</span>
                    <span className="font-semibold text-neutral-900">
                      {formatPrice(booking.total)}
                    </span>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </main>

      <BottomNavigation />
    </div>
  );
}
