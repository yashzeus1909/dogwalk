import React, { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { toast } from "@/hooks/use-toast";
import { apiRequest, queryClient } from "@/lib/queryClient";
import { User, Booking, Walker, updateUserProfileSchema } from "@shared/schema";
import Header from "@/components/header";
import BottomNavigation from "@/components/bottom-navigation";
import { 
  User as UserIcon, 
  Mail, 
  Phone, 
  MapPin, 
  Calendar, 
  Clock, 
  DollarSign,
  Edit,
  Save,
  X,
  Shield,
  Heart,
  Star
} from "lucide-react";

// For demo purposes, we'll use a mock user ID
const MOCK_USER_ID = 1;

const profileFormSchema = updateUserProfileSchema.extend({
  email: z.string().email("Please enter a valid email address").optional().or(z.literal("")).transform(val => val || undefined),
  phone: z.string().min(10, "Phone number must be at least 10 digits").optional().or(z.literal("")).transform(val => val || undefined),
  firstName: z.string().optional().transform(val => val || undefined),
  lastName: z.string().optional().transform(val => val || undefined),
  address: z.string().optional().transform(val => val || undefined),
  profileImage: z.string().optional().transform(val => val || undefined),
  emergencyContact: z.string().optional().transform(val => val || undefined),
  emergencyPhone: z.string().optional().transform(val => val || undefined),
});

type ProfileFormData = z.infer<typeof profileFormSchema>;

export default function Profile() {
  const [isEditing, setIsEditing] = useState(false);

  // Fetch user profile
  const { data: user, isLoading: userLoading } = useQuery({
    queryKey: ['/api/profile', MOCK_USER_ID],
    queryFn: async () => {
      const response = await fetch(`/api/profile/${MOCK_USER_ID}`);
      if (!response.ok) {
        throw new Error('Failed to fetch profile');
      }
      return response.json() as Promise<User>;
    },
  });

  // Fetch user bookings
  const { data: bookings = [], isLoading: bookingsLoading } = useQuery({
    queryKey: ['/api/profile', MOCK_USER_ID, 'bookings'],
    queryFn: async () => {
      const response = await fetch(`/api/profile/${MOCK_USER_ID}/bookings`);
      if (!response.ok) {
        throw new Error('Failed to fetch bookings');
      }
      return response.json() as Promise<Booking[]>;
    },
  });

  // Fetch walkers for booking details
  const { data: walkers = [] } = useQuery({
    queryKey: ['/api/walkers'],
    queryFn: async () => {
      const response = await fetch('/api/walkers');
      if (!response.ok) {
        throw new Error('Failed to fetch walkers');
      }
      return response.json() as Promise<Walker[]>;
    },
  });

  const form = useForm<ProfileFormData>({
    resolver: zodResolver(profileFormSchema),
    defaultValues: {
      email: "",
      firstName: "",
      lastName: "",
      phone: "",
      address: "",
      profileImage: "",
      emergencyContact: "",
      emergencyPhone: "",
    },
  });

  // Update form values when user data loads
  React.useEffect(() => {
    if (user) {
      form.reset({
        email: user.email || "",
        firstName: user.firstName || "",
        lastName: user.lastName || "",
        phone: user.phone || "",
        address: user.address || "",
        profileImage: user.profileImage || "",
        emergencyContact: user.emergencyContact || "",
        emergencyPhone: user.emergencyPhone || "",
      });
    }
  }, [user, form]);

  const updateProfileMutation = useMutation({
    mutationFn: async (data: ProfileFormData) => {
      const response = await fetch(`/api/profile/${MOCK_USER_ID}`, {
        method: 'PATCH',
        body: JSON.stringify(data),
        headers: {
          'Content-Type': 'application/json',
        },
      });
      if (!response.ok) {
        throw new Error('Failed to update profile');
      }
      return response.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/profile', MOCK_USER_ID] });
      toast({
        title: "Profile updated",
        description: "Your profile has been successfully updated.",
      });
      setIsEditing(false);
    },
    onError: (error: any) => {
      toast({
        title: "Error updating profile",
        description: error.message || "Failed to update profile. Please try again.",
        variant: "destructive",
      });
    },
  });

  const onSubmit = (data: ProfileFormData) => {
    updateProfileMutation.mutate(data);
  };

  const handleCancel = () => {
    form.reset();
    setIsEditing(false);
  };

  const getWalkerById = (walkerId: number) => {
    return walkers.find(w => w.id === walkerId);
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'confirmed':
        return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
      case 'pending':
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
      case 'cancelled':
        return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
      case 'completed':
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
  };

  if (userLoading) {
    return (
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
        <Header />
        <div className="flex items-center justify-center p-8">
          <div className="text-center">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p className="text-gray-600 dark:text-gray-400">Loading profile...</p>
          </div>
        </div>
        <BottomNavigation />
      </div>
    );
  }

  const displayName = user?.firstName && user?.lastName 
    ? `${user.firstName} ${user.lastName}`
    : user?.username || 'User';

  const initials = user?.firstName && user?.lastName
    ? `${user.firstName[0]}${user.lastName[0]}`
    : user?.username?.[0]?.toUpperCase() || 'U';

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
      <Header />
      
      <div className="max-w-4xl mx-auto p-4 pb-20">
        {/* Profile Header */}
        <Card className="mb-6">
          <CardHeader>
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-4">
                <Avatar className="h-20 w-20">
                  <AvatarImage src={user?.profileImage || ""} alt={displayName} />
                  <AvatarFallback className="text-lg font-semibold">
                    {initials}
                  </AvatarFallback>
                </Avatar>
                <div>
                  <CardTitle className="text-2xl">{displayName}</CardTitle>
                  <CardDescription className="text-lg">@{user?.username}</CardDescription>
                  {user?.email && (
                    <div className="flex items-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                      <Mail className="h-4 w-4 mr-2" />
                      {user.email}
                    </div>
                  )}
                </div>
              </div>
              <Button
                onClick={() => setIsEditing(!isEditing)}
                variant={isEditing ? "outline" : "default"}
                size="sm"
              >
                {isEditing ? (
                  <>
                    <X className="h-4 w-4 mr-2" />
                    Cancel
                  </>
                ) : (
                  <>
                    <Edit className="h-4 w-4 mr-2" />
                    Edit Profile
                  </>
                )}
              </Button>
            </div>
          </CardHeader>
        </Card>

        <Tabs defaultValue="profile" className="space-y-6">
          <TabsList className="grid w-full grid-cols-3">
            <TabsTrigger value="profile">Profile</TabsTrigger>
            <TabsTrigger value="bookings">My Bookings</TabsTrigger>
            <TabsTrigger value="settings">Settings</TabsTrigger>
          </TabsList>

          {/* Profile Tab */}
          <TabsContent value="profile" className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Personal Information</CardTitle>
                <CardDescription>
                  {isEditing ? "Update your personal details" : "Your personal details"}
                </CardDescription>
              </CardHeader>
              <CardContent>
                {isEditing ? (
                  <Form {...form}>
                    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <FormField
                          control={form.control}
                          name="firstName"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>First Name</FormLabel>
                              <FormControl>
                                <Input placeholder="Enter your first name" {...field} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                        <FormField
                          control={form.control}
                          name="lastName"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Last Name</FormLabel>
                              <FormControl>
                                <Input placeholder="Enter your last name" {...field} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>
                      
                      <FormField
                        control={form.control}
                        name="email"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Email</FormLabel>
                            <FormControl>
                              <Input type="email" placeholder="Enter your email" {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      
                      <FormField
                        control={form.control}
                        name="phone"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Phone Number</FormLabel>
                            <FormControl>
                              <Input placeholder="Enter your phone number" {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      
                      <FormField
                        control={form.control}
                        name="address"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Address</FormLabel>
                            <FormControl>
                              <Textarea placeholder="Enter your address" {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      
                      <Separator />
                      
                      <div className="space-y-4">
                        <h4 className="font-medium">Emergency Contact</h4>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <FormField
                            control={form.control}
                            name="emergencyContact"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Emergency Contact Name</FormLabel>
                                <FormControl>
                                  <Input placeholder="Enter emergency contact name" {...field} />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          <FormField
                            control={form.control}
                            name="emergencyPhone"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Emergency Contact Phone</FormLabel>
                                <FormControl>
                                  <Input placeholder="Enter emergency contact phone" {...field} />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      </div>
                      
                      <div className="flex justify-end space-x-2 pt-4">
                        <Button type="button" variant="outline" onClick={handleCancel}>
                          Cancel
                        </Button>
                        <Button type="submit" disabled={updateProfileMutation.isPending}>
                          {updateProfileMutation.isPending ? (
                            <>
                              <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                              Saving...
                            </>
                          ) : (
                            <>
                              <Save className="h-4 w-4 mr-2" />
                              Save Changes
                            </>
                          )}
                        </Button>
                      </div>
                    </form>
                  </Form>
                ) : (
                  <div className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div className="space-y-4">
                        <div>
                          <Label className="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</Label>
                          <p className="mt-1">{displayName}</p>
                        </div>
                        <div>
                          <Label className="text-sm font-medium text-gray-500 dark:text-gray-400">Email</Label>
                          <p className="mt-1">{user?.email || "Not provided"}</p>
                        </div>
                        <div>
                          <Label className="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</Label>
                          <p className="mt-1">{user?.phone || "Not provided"}</p>
                        </div>
                      </div>
                      <div className="space-y-4">
                        <div>
                          <Label className="text-sm font-medium text-gray-500 dark:text-gray-400">Address</Label>
                          <p className="mt-1">{user?.address || "Not provided"}</p>
                        </div>
                        <div>
                          <Label className="text-sm font-medium text-gray-500 dark:text-gray-400">Emergency Contact</Label>
                          <p className="mt-1">{user?.emergencyContact || "Not provided"}</p>
                        </div>
                        <div>
                          <Label className="text-sm font-medium text-gray-500 dark:text-gray-400">Emergency Phone</Label>
                          <p className="mt-1">{user?.emergencyPhone || "Not provided"}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          {/* Bookings Tab */}
          <TabsContent value="bookings" className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>My Bookings</CardTitle>
                <CardDescription>Your recent and upcoming dog walking bookings</CardDescription>
              </CardHeader>
              <CardContent>
                {bookingsLoading ? (
                  <div className="text-center py-8">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p className="text-gray-600 dark:text-gray-400">Loading bookings...</p>
                  </div>
                ) : bookings.length === 0 ? (
                  <div className="text-center py-8">
                    <Heart className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                    <p className="text-gray-600 dark:text-gray-400">No bookings yet</p>
                    <p className="text-sm text-gray-500 dark:text-gray-500">Book your first dog walker to get started!</p>
                  </div>
                ) : (
                  <div className="space-y-4">
                    {bookings.map((booking) => {
                      const walker = getWalkerById(booking.walkerId);
                      return (
                        <Card key={booking.id} className="border-l-4 border-l-blue-500">
                          <CardContent className="p-4">
                            <div className="flex items-center justify-between mb-3">
                              <div className="flex items-center space-x-3">
                                {walker && (
                                  <Avatar className="h-10 w-10">
                                    <AvatarImage src={walker.image} alt={walker.name} />
                                    <AvatarFallback>{walker.name[0]}</AvatarFallback>
                                  </Avatar>
                                )}
                                <div>
                                  <h4 className="font-semibold">{walker?.name || "Unknown Walker"}</h4>
                                  <p className="text-sm text-gray-600 dark:text-gray-400">
                                    Walking {booking.dogName} ({booking.dogSize})
                                  </p>
                                </div>
                              </div>
                              <Badge className={getStatusColor(booking.status)}>
                                {booking.status}
                              </Badge>
                            </div>
                            
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                              <div className="flex items-center space-x-2">
                                <Calendar className="h-4 w-4 text-gray-500" />
                                <span>{booking.date}</span>
                              </div>
                              <div className="flex items-center space-x-2">
                                <Clock className="h-4 w-4 text-gray-500" />
                                <span>{booking.time} ({booking.duration}min)</span>
                              </div>
                              <div className="flex items-center space-x-2">
                                <DollarSign className="h-4 w-4 text-gray-500" />
                                <span>${(booking.total / 100).toFixed(2)}</span>
                              </div>
                              <div className="flex items-center space-x-2">
                                <Phone className="h-4 w-4 text-gray-500" />
                                <span>{booking.phone}</span>
                              </div>
                            </div>
                            
                            {booking.instructions && (
                              <div className="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p className="text-sm"><strong>Instructions:</strong> {booking.instructions}</p>
                              </div>
                            )}
                          </CardContent>
                        </Card>
                      );
                    })}
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          {/* Settings Tab */}
          <TabsContent value="settings" className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Account Settings</CardTitle>
                <CardDescription>Manage your account preferences and security</CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="space-y-4">
                  <div className="flex items-center justify-between">
                    <div>
                      <h4 className="font-medium">Notifications</h4>
                      <p className="text-sm text-gray-600 dark:text-gray-400">Receive booking updates and reminders</p>
                    </div>
                    <Button variant="outline" size="sm">
                      Configure
                    </Button>
                  </div>
                  
                  <Separator />
                  
                  <div className="flex items-center justify-between">
                    <div>
                      <h4 className="font-medium">Privacy Settings</h4>
                      <p className="text-sm text-gray-600 dark:text-gray-400">Control your profile visibility</p>
                    </div>
                    <Button variant="outline" size="sm">
                      Manage
                    </Button>
                  </div>
                  
                  <Separator />
                  
                  <div className="flex items-center justify-between">
                    <div>
                      <h4 className="font-medium">Security</h4>
                      <p className="text-sm text-gray-600 dark:text-gray-400">Change password and security settings</p>
                    </div>
                    <Button variant="outline" size="sm">
                      <Shield className="h-4 w-4 mr-2" />
                      Security
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader>
                <CardTitle>Account Statistics</CardTitle>
                <CardDescription>Your platform usage overview</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div className="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div className="text-2xl font-bold text-blue-600 dark:text-blue-400">{bookings.length}</div>
                    <div className="text-sm text-gray-600 dark:text-gray-400">Total Bookings</div>
                  </div>
                  <div className="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div className="text-2xl font-bold text-green-600 dark:text-green-400">
                      {bookings.filter(b => b.status === 'completed').length}
                    </div>
                    <div className="text-sm text-gray-600 dark:text-gray-400">Completed</div>
                  </div>
                  <div className="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div className="text-2xl font-bold text-purple-600 dark:text-purple-400">
                      ${bookings.reduce((sum, b) => sum + (b.total / 100), 0).toFixed(0)}
                    </div>
                    <div className="text-sm text-gray-600 dark:text-gray-400">Total Spent</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
      
      <BottomNavigation />
    </div>
  );
}